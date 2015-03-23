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

namespace ptlis\CoverageMonitor\Unified;

/**
 * Aggregate file data.
 */
class RawFileList
{
    /**
     * @var array File data indexed by filename.
     */
    private $fileData = array();


    /**
     * Constructor.
     *
     * @throws \RuntimeException
     *
     * @todo Handle home directory
     *
     * @param string $projectDirectory
     * @param string[] $pathFilterList
     */
    public function __construct($projectDirectory, array $pathFilterList = array())
    {
        $composerPath = $projectDirectory . DIRECTORY_SEPARATOR . 'composer.json';
        if (!file_exists($composerPath)) {
            throw new \RuntimeException('composer.json not found');
        }

        $composerData = json_decode(file_get_contents($composerPath), true);
        if (false === $composerData) {
            throw new \RuntimeException('composer.json not found');
        }

        $validatedFileList = $this->getFileList($projectDirectory, $pathFilterList, $composerData);

        foreach ($validatedFileList as $validatedFile) {
            $fileName = str_replace($projectDirectory . DIRECTORY_SEPARATOR, '', $validatedFile);
            $this->fileData[$fileName] = file($validatedFile, FILE_IGNORE_NEW_LINES);
        }
    }

    /**
     * Get all file data.
     *
     * @return array
     */
    public function getFiles()
    {
        return $this->fileData;
    }

    /**
     * Get the contents of the specified file.
     *
     * @todo Should we really return an empty array here? Would an exception make more sense?
     *
     * @param string $name
     *
     * @return array
     */
    public function getFile($name)
    {
        $file = array();
        if (array_key_exists($name, $this->fileData)) {
            $file = $this->fileData[$name];
        }
        return $file;
    }

    /**
     * @param string $projectDirectory
     * @param string[] $pathFilterList
     * @param array $composerData
     *
     * @return string[]
     */
    private function getFileList($projectDirectory, array $pathFilterList, array $composerData)
    {
        $directoryList = $this->getCodeDirectories($composerData, $pathFilterList);

        $validatedFileList = array();
        foreach ($directoryList as $directory) {
            $baseDirectory = $projectDirectory . DIRECTORY_SEPARATOR . $directory;
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($baseDirectory),
                \RecursiveIteratorIterator::SELF_FIRST
            );

            /** @var \SplFileInfo $path */
            foreach ($iterator as $path) {
                if ('.php' === substr($path, -4, 4)) {
                    $validatedFileList[] = $path->getPath() . DIRECTORY_SEPARATOR . $path->getFilename();
                }
            }
        }

        return $validatedFileList;
    }

    /**
     * Get a list of directories that contain code - read from composer.json & filtered by passed path list.
     *
     * @param array $composerData
     *
     * @return string[]
     */
    private function getCodeDirectories(array $composerData, array $pathFilterList)
    {
        $codeDirectoryList = array();

        if (array_key_exists('autoload', $composerData)) {
            foreach ($composerData['autoload'] as $directoryList) {
                $codeDirectoryList = array_merge(
                    $codeDirectoryList,
                    $this->getValidatedDirectoryList($directoryList, $pathFilterList)
                );
            }
        }

        return $codeDirectoryList;
    }

    /**
     * Get a list of directories that match provided code paths.
     *
     * @param string|array $directoryList
     * @param array $pathFilterList
     * @return array
     */
    private function getValidatedDirectoryList($directoryList, array $pathFilterList) {
        $firstPassList = array();

        if (is_array($directoryList)) {
            foreach ($directoryList as $directory) {
                $firstPassList[] = $directory;
            }
        } else {
            $firstPassList[] = $directoryList;
        }

        $validatedDirectoryList = array();
        foreach ($firstPassList as $directory) {
            $directory = trim($directory, DIRECTORY_SEPARATOR);

            // If there are paths
            if (!count($pathFilterList) || in_array($directory, $pathFilterList)) {
                $validatedDirectoryList[] = $directory;
            }
        }

        return $validatedDirectoryList;
    }
}
