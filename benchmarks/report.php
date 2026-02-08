#!/usr/bin/env php
<?php

/*
 * This file is part of fab2s/Math.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/Math
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

$dumpFile = sys_get_temp_dir() . '/phpbench-' . uniqid() . '.xml';

// Forward all CLI arguments to phpbench
$args = array_slice($argv, 1);
$cmd  = sprintf(
    'XDEBUG_MODE=off %s vendor/bin/phpbench run --dump-file=%s %s 2>&1',
    PHP_BINARY,
    escapeshellarg($dumpFile),
    implode(' ', array_map('escapeshellarg', $args)),
);

// Run phpbench, show progress on stderr
$proc = popen($cmd, 'r');
while (($line = fgets($proc)) !== false) {
    fwrite(STDERR, $line);
}
pclose($proc);

if (! file_exists($dumpFile)) {
    fwrite(STDERR, "No dump file generated\n");
    exit(1);
}

$xml = simplexml_load_file($dumpFile);
unlink($dumpFile);
if (! $xml) {
    fwrite(STDERR, "Failed to parse benchmark XML\n");
    exit(1);
}

$subjects = [];
foreach ($xml->suite->benchmark as $benchmark) {
    foreach ($benchmark->subject as $subject) {
        $name            = (string) $subject['name'];
        $stats           = $subject->variant->stats;
        $subjects[$name] = [
            'mode'   => (float) $stats['mode'],
            'rstdev' => (float) $stats['rstdev'],
        ];
    }
}

// Pair fab2s_* with brick_* by operation name
$pairs = [];
foreach ($subjects as $name => $data) {
    if (! str_starts_with($name, 'fab2s_')) {
        continue;
    }

    $op    = substr($name, 6);
    $brick = 'brick_' . $op;
    if (! isset($subjects[$brick])) {
        continue;
    }

    $pairs[$op] = [
        'fab2s' => $data,
        'brick' => $subjects[$brick],
    ];
}

if (empty($pairs)) {
    fwrite(STDERR, "No paired benchmarks found\n");
    exit(1);
}

function formatTime(float $us): string
{
    if ($us >= 1000) {
        return sprintf('%.2fms', $us / 1000);
    }

    return sprintf('%.3fμs', $us);
}

// Output markdown
echo "\n| Operation | fab2s/math | brick/math | Factor |\n";
echo "|---|---:|---:|---:|\n";

foreach ($pairs as $op => $pair) {
    $fab2s  = $pair['fab2s']['mode'];
    $brick  = $pair['brick']['mode'];
    $factor = $brick / max($fab2s, 0.001);
    $winner = $fab2s <= $brick ? '**' : '';
    $loser  = $fab2s > $brick ? '**' : '';

    echo sprintf(
        "| %s | %s%s (±%.1f%%)%s | %s%s (±%.1f%%)%s | %.2fx |\n",
        str_replace('_', ' ', $op),
        $winner,
        formatTime($fab2s),
        $pair['fab2s']['rstdev'],
        $winner,
        $loser,
        formatTime($brick),
        $pair['brick']['rstdev'],
        $loser,
        $factor,
    );
}
