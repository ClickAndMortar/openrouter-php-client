#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Fails when the live OpenRouter API exposes an operation that api-coverage.json
 * has never been reviewed against.
 *
 * Every operation must be listed either in `covered` (a typed SDK wrapper exists)
 * or in `known_gaps` (reviewed, deliberately not implemented yet). Anything else
 * is drift that appeared since the last review.
 *
 * Usage:
 *   php bin/check-api-drift.php                 # fetch the live spec
 *   php bin/check-api-drift.php path/to.yaml    # check a local spec instead
 */

const SPEC_URL = 'https://openrouter.ai/openapi.yaml';
const METHODS = ['get', 'post', 'put', 'patch', 'delete'];

$root = dirname(__DIR__);
$manifestPath = $root.'/api-coverage.json';

$manifest = json_decode((string) file_get_contents($manifestPath), true, 512, JSON_THROW_ON_ERROR);
$reviewed = array_flip([...$manifest['covered'], ...$manifest['known_gaps']]);

$specPath = $argv[1] ?? null;
if ($specPath === null) {
    $specPath = $root.'/openapi-openrouter.yaml';
    fwrite(STDERR, 'Fetching '.SPEC_URL."\n");
    $spec = @file_get_contents(SPEC_URL);
    if ($spec === false) {
        fwrite(STDERR, "Could not fetch the live spec; falling back to the vendored copy.\n");
    } else {
        file_put_contents($specPath, $spec);
    }
}

$operations = extractOperations($specPath);

if ($operations === []) {
    fwrite(STDERR, "No operations found in {$specPath} - is it a valid OpenAPI document?\n");
    exit(2);
}

$unreviewed = array_values(array_filter(
    $operations,
    static fn (string $op): bool => ! isset($reviewed[$op]),
));

$vanished = array_values(array_filter(
    array_keys($reviewed),
    static fn (string $op): bool => ! in_array($op, $operations, true),
));

printf("Live spec: %d operations. Reviewed: %d.\n", count($operations), count($reviewed));

if ($vanished !== []) {
    printf("\n%d operation(s) in api-coverage.json no longer exist upstream:\n", count($vanished));
    foreach ($vanished as $op) {
        echo "  - {$op}\n";
    }
}

if ($unreviewed !== []) {
    printf("\n%d NEW operation(s) not present in api-coverage.json:\n", count($unreviewed));
    foreach ($unreviewed as $op) {
        echo "  + {$op}\n";
    }
    echo "\nAdd each one to `covered` (once wrapped) or `known_gaps` (reviewed, deferred),\n";
    echo "then bump `reviewed_at`.\n";
    exit(1);
}

if ($vanished !== []) {
    exit(1);
}

echo "\nNo API drift.\n";
exit(0);

/**
 * Minimal path/method scanner. Reads the `paths:` block by indentation rather
 * than parsing YAML, so the check needs no ext-yaml or Composer dependency.
 *
 * @return list<string>
 */
function extractOperations(string $path): array
{
    $handle = fopen($path, 'r');
    if ($handle === false) {
        fwrite(STDERR, "Cannot read {$path}\n");
        exit(2);
    }

    $operations = [];
    $inPaths = false;
    $currentPath = null;

    while (($line = fgets($handle)) !== false) {
        $line = rtrim($line, "\r\n");

        if (preg_match('/^paths:\s*$/', $line) === 1) {
            $inPaths = true;

            continue;
        }

        if (! $inPaths) {
            continue;
        }

        // A new top-level key ends the paths block.
        if ($line !== '' && $line[0] !== ' ' && $line[0] !== '#') {
            break;
        }

        if (preg_match('/^  (\/\S*):\s*$/', $line, $m) === 1) {
            $currentPath = $m[1];

            continue;
        }

        if ($currentPath !== null && preg_match('/^    ([a-z]+):\s*$/', $line, $m) === 1) {
            if (in_array($m[1], METHODS, true)) {
                $operations[] = strtoupper($m[1]).' '.$currentPath;
            }
        }
    }

    fclose($handle);
    sort($operations);

    return $operations;
}
