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
 * Adapter wrapping PHPUnit's \PHP_CodeCoverage_Report_Node_Directory class & exposing a more convenient interface for
 *  the purpose of this library.
 */
class CoverageDirectory
{
    private $directory;

    private $workingDirectory;

    public function __construct(\PHP_CodeCoverage_Report_Node_Directory $directory, $workingDirectory)
    {
        $this->directory = $directory;
        $this->workingDirectory = realpath($workingDirectory);

        // validation
        $containingDirectory = substr($this->directory->getPath(), 0, strlen($this->workingDirectory));
        if ($containingDirectory !== $this->workingDirectory) {
            throw new \RuntimeException('Incorrect working directory provided.');
        }
    }

    /**
     * Get any subdirectories.
     *
     * @return CoverageDirectory[]
     */
    public function getDirectories()
    {
        $directoryList = array();
        foreach ($this->directory->getDirectories() as $directory) {
            $directoryList[] = new CoverageDirectory($directory, $this->workingDirectory);
        }
        return $directoryList;
    }

    /**
     * Get files in this directory.
     *
     * @return CoverageFile[]
     */
    public function getFiles()
    {
        $fileList = array();
        foreach ($this->directory->getFiles() as $file) {
            $fileList[] = new CoverageFile($file, $this->workingDirectory);
        }
        return $fileList;
    }

    public function getName()
    {
        return $this->directory->getName();
    }

    /**
     * Get the relative path to the file.
     *
     * @return string
     */
    public function getPath()
    {
        $fullPath = $this->directory->getPath();
        $path = substr($fullPath, strlen($this->workingDirectory), strlen($fullPath));
        if (DIRECTORY_SEPARATOR === $path[0]) {
            $path = substr($path, 1, strlen($path));
        }

        return $path;
    }
}
