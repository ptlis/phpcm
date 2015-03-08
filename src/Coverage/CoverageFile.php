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
 * Contains the data for a single file's coverage data.
 */
class CoverageFile
{
    /**
     * @var string The full path to the file.
     */
    private $filePath;

    /**
     * @var CoverageLine[] array of coverage lines for this file.
     */
    private $lineList;

    /**
     * @var string  The working directory during test execution.
     */
    private $workingDirectory;


    /**
     * Constructor.
     *
     * @param string $filePath
     * @param array $lineList
     * @param string $workingDirectory
     */
    public function __construct($filePath, array $lineList, $workingDirectory)
    {
        $this->filePath = $filePath;
        $this->lineList = $lineList;
        $this->workingDirectory = $workingDirectory;

        // validation
        $containingDirectory = substr($this->filePath, 0, strlen($this->workingDirectory));
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
        $pathData = explode(DIRECTORY_SEPARATOR, $this->filePath);

        return array_pop($pathData);
    }

    /**
     * Get the full path of the file
     *
     * @return string
     */
    public function getFullPath()
    {
        return $this->filePath;
    }

    /**
     * Get the relative path of the file.
     *
     * @return string
     */
    public function getRelativePath()
    {
        $path = substr($this->filePath, strlen($this->workingDirectory), strlen($this->filePath));
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
        return $this->lineList;
    }
}
