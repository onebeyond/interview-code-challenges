<?php

$cloverFile = __DIR__ . '/../coverage.clover';

if (!file_exists($cloverFile)) {
    echo "Error: coverage.clover not found. Run tests first.\n";
    exit(1);
}

$xml = simplexml_load_file($cloverFile);
if ($xml === false) {
    echo "Error: Failed to parse coverage.clover XML.\n";
    exit(1);
}
$metrics = $xml->project->metrics;
$elements = (int) $metrics['elements'];
$covered = (int) $metrics['coveredelements'];

$coverage = $elements > 0 ? ($covered / $elements) * 100 : 0;
$threshold = 85.0;

printf("Strict Code Coverage: %.2f%%\n", $coverage);

if ($coverage < $threshold) {
    echo "❌ FAILED: Coverage is below the {$threshold}% threshold.\n";
    exit(1);
}

echo "✅ PASSED: Coverage meets enterprise standards.\n";
exit(0);
