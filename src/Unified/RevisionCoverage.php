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

use ptlis\CoverageMonitor\Coverage\Interfaces\CoverageInterface;
use ptlis\CoverageMonitor\Unified\Interfaces\FileInterface;
use ptlis\DiffParser\Changeset;

/**
 * Contains the coverage and changeset data for a single VCS revision.
 */
class RevisionCoverage
{
    /**
     * @var CoverageInterface
     */
    private $coverage;

    /**
     * @var Changeset
     */
    private $changeset;


    /**
     * Constructor.
     *
     * @param CoverageInterface $coverage
     * @param Changeset $changeset
     */
    public function __construct(
        CoverageInterface $coverage,
        Changeset $changeset
    ) {
        $this->coverage = $coverage;
        $this->changeset = $changeset;
    }

    /**
     * Get a the merged file list.
     *
     * @return FileInterface[]
     */
    public function getFiles()
    {
        $mergedFileList = array();
        foreach ($this->coverage->getFiles() as $coverageFile) {
            $found = false;
            $rawFileLineList = file($coverageFile->getFullPath(), FILE_IGNORE_NEW_LINES);

            foreach ($this->changeset->getFiles() as $changedFile) {
                if ($changedFile->getNewFilename() === $coverageFile->getRelativePath()) {
                    $mergedFileList[] = new FileChanged(
                        $coverageFile,
                        $changedFile,
                        $rawFileLineList
                    );
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $mergedFileList[] = new FileUnchanged(
                    $coverageFile,
                    $rawFileLineList
                );
            }
        }

        return $mergedFileList;
    }
}
