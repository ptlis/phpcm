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

/**
 * Adapter wrapping PHPUnit's \PHP_CodeCoverage_Report_Node_File class & exposing a more convenient interface for
 *  the purpose of this library.
 */
class CoverageFile
{
    /**
     * @var \PHP_CodeCoverage_Report_Node_File  PHPUnit coverage object.
     */
    private $file;

    /**
     * @var string  The working directory during test execution.
     */
    private $workingDirectory;


    /**
     * Constructor.
     *
     * @param \PHP_CodeCoverage_Report_Node_File $file
     * @param string $workingDirectory
     */
    public function __construct(\PHP_CodeCoverage_Report_Node_File $file, $workingDirectory)
    {
        $this->file = $file;
        $this->workingDirectory = realpath($workingDirectory);

        // validation
        $containingDirectory = substr($this->file->getPath(), 0, strlen($this->workingDirectory));
        if ($containingDirectory !== $this->workingDirectory) {
            throw new \RuntimeException('Incorrect working directory provided.');
        }
    }

    /**
     * The filename.
     *
     * @return string
     */
    public function getName()
    {
        return $this->file->getName();
    }

    /**
     * Get the relative path to the file.
     *
     * @return string
     */
    public function getPath()
    {
        $fullPath = $this->file->getPath();
        $path = substr($fullPath, strlen($this->workingDirectory), strlen($fullPath));
        if (DIRECTORY_SEPARATOR === $path[0]) {
            $path = substr($path, 1, strlen($path));
        }

        return $path;
    }

    /**
     * Get the processed lines.
     *
     * @return CoverageLine[]
     */
    public function getLines()
    {
        $coverageData = $this->file->getCoverageData();
        $ignoredLines = $this->file->getIgnoredLines();
        $testData = $this->file->getTestData();
        $lines = file($this->file->getPath(), FILE_IGNORE_NEW_LINES);

        $processedLineList = array();
        for ($i = 0; $i < count($lines); $i++) {
            $lineNo = $i + 1;

            if (array_key_exists($lineNo, $ignoredLines)) {
                $state = CoverageLine::SKIPPED;

            } elseif (array_key_exists($lineNo, $coverageData)) {

                // TODO: Stupid solution, need to figure out constants used around line 365 in
                // \PHP_CodeCoverage_Report_HTML_Renderer_File to be smarter
                $successCount = 0;
                $notSuccessCount = 0;

                if (is_array($coverageData[$lineNo])) {
                    foreach ($coverageData[$lineNo] as $test) {
                        if (array_key_exists($test, $testData)) {
                            if (0 === $testData[$test]) {
                                $successCount++;
                            } else {
                                $notSuccessCount++;
                            }
                        }
                    }
                }


                // No tests for this line
                if (0 == count($coverageData[$lineNo])) {
                    $state = CoverageLine::UNCOVERED;

                // All successful
                } elseif (0 === $notSuccessCount && $successCount > 0) {
                    $state = CoverageLine::SUCCESS;

                // None successful
                } elseif (0 === $successCount && $notSuccessCount > 0) {
                    $state = CoverageLine::ERROR;

                // Partial success
                } else {
                    $state = CoverageLine::PARTIAL_SUCCESS;
                }

            } else {
                $state = CoverageLine::SKIPPED;
            }

            $processedLineList[] = new CoverageLine($lineNo, $lines[$i], $state);
        }

        return $processedLineList;
    }
}
