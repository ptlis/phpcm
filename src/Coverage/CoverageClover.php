<?php

/**
 * PHP Version 5.3
 *
 * @copyright   (c) 2015 brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ptlis\CoverageMonitor\Coverage;

use ptlis\CoverageMonitor\Coverage\Interfaces\CoverageInterface;

/**
 * Coverage implementation for Clover XML format.
 */
class CoverageClover implements CoverageInterface
{
    /**
     * @var CoverageFile[]  Array of files with code coverage.
     */
    private $fileList = array();

    /**
     * @var string Path to the working directory.
     */
    private $workingDirectory;


    /**
     * Constructor.
     *
     * @param string $pathToCoverage
     */
    public function __construct($pathToCoverage, $workingDirectory)
    {
        $this->workingDirectory = $workingDirectory;

        libxml_use_internal_errors(true);
        $clover = simplexml_load_file($pathToCoverage);

        if (false === $clover) {
            throw new \RuntimeException(
                'Provided path "' . $pathToCoverage . '" does not point to a valid XML document.'
            );
        }

        $this->parseCloverXML($clover);
    }

    /**
     * {@inheritdoc}
     */
    public function getFiles()
    {
        return $this->fileList;
    }

    /**
     * Read coverage data for a line.
     *
     * @param \SimpleXMLElement $line
     * @param string[] $fileLines
     *
     * @return CoverageLine
     */
    private function readLine(\SimpleXMLElement $line, array $fileLines)
    {
        $attributes = $line->attributes();

        $lineNo = intval($attributes['num']);
        $count = intval($attributes['count']);
        $content = $fileLines[$lineNo - 1]; // Account for indexing of array = 0 when lineNo = 1

        return new CoverageLine(
            $lineNo,
            $content,
            $count
        );
    }

    /**
     * Parse out the data we need from Clover XML.
     *
     * @param \SimpleXMLElement $clover
     */
    private function parseCloverXML(\SimpleXMLElement $clover)
    {
        // Read file & line coverage data
        /** @var \SimpleXMLElement $project */
        foreach ($clover->children() as $project) {
            if ('project' === $project->getName()) {
                $this->readProjectData($project);
            }
        }
    }

    /**
     * Read project data from Clover coverage.
     *
     * @param \SimpleXMLElement $project
     */
    private function readProjectData(\SimpleXMLElement $project)
    {
        /** @var \SimpleXMLElement $child */
        foreach ($project->children() as $child) {
            switch ($child->getName()) {
                case 'file':
                    $this->readFileData($child);
                    break;

                case 'package':
                    $this->readPackageData($child);
                    break;

                case 'metrics':
                    // TODO: Implement?
                    break;
            }
        }
    }

    /**
     * Read coverage data for a file.
     *
     * @param \SimpleXMLElement $file
     */
    private function readFileData(\SimpleXMLElement $file)
    {
        // Class with coverage data
        if (!empty($file->class)) {
            $attributes = $file->attributes();
            $filename = strval($attributes['name']);
            $fileLines = file($filename, FILE_IGNORE_NEW_LINES);

            $lineList = array();

            /** @var \SimpleXMLElement $child */
            foreach ($file->children() as $child) {
                if ('line' === $child->getName()) {
                    $lineList[] = $this->readLine($child, $fileLines);
                }
            }

            $this->fileList[] = new CoverageFile(
                $filename,
                $lineList,
                $this->workingDirectory
            );
        }
    }

    /**
     * Read coverage data for a package.
     *
     * @param \SimpleXMLElement $package
     */
    private function readPackageData(\SimpleXMLElement $package)
    {
        /** @var \SimpleXMLElement $file */
        foreach ($package->children() as $file) {
            if ('file' === $file->getName()) {
                $this->readFileData($file);
            }
        }
    }
}
