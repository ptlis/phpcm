<?php

require_once realpath(__DIR__ . '/../vendor/autoload.php');


// Handle coverage paths
$basePath = realpath(__DIR__ . '/..');

$dataDirectory = $basePath . '/tests/data/';
foreach (new DirectoryIterator($dataDirectory) as $directory) {
    if (!$directory->isDot()) {

        $baseCoveragePath = $dataDirectory . $directory . '/coverage.clover.base';
        $realCoveragePath = $dataDirectory . $directory . '/coverage.clover';

        if (file_exists($baseCoveragePath)) {
            $coverageData = file_get_contents($baseCoveragePath);
            $coverageData = str_replace('___PATH___', $basePath, $coverageData);
            file_put_contents($realCoveragePath, $coverageData);
        }
    }
}
