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
use ptlis\Vcs\Shared\RevisionMeta;

/**
 * Contains the coverage and changeset data for a single VCS revision.
 */
class RevisionCoverage
{
    /**
     * @var RevisionMeta
     */
    private $revision;

    /**
     * @var CoverageInterface
     */
    private $coverage;

    /**
     * @var Changeset
     */
    private $changeset;

    /**
     * @var RawFileList
     */
    private $rawFileList;

    /**
     * @var FileInterface[]
     */
    private $lineList;


    /**
     * Constructor.
     *
     * @param RevisionMeta $revision
     * @param CoverageInterface $coverage
     * @param Changeset $changeset
     * @param RawFileList $rawFileList
     */
    public function __construct(
        RevisionMeta $revision,
        CoverageInterface $coverage,
        Changeset $changeset,
        RawFileList $rawFileList
    ) {
        $this->revision = $revision;
        $this->coverage = $coverage;
        $this->changeset = $changeset;
        $this->rawFileList = $rawFileList;
        $this->lineList = $this->buildLines();
    }

    /**
     * Get a the merged file list.
     *
     * @return FileInterface[]
     */
    public function getFiles()
    {
        return $this->lineList;
    }

    /**
     * Get the revision identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->revision->getIdentifier();
    }

    /**
     * Get the author of this commit.
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->revision->getAuthor();
    }

    /**
     * Get the created date/time.
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->revision->getCreated();
    }

    /**
     * Get the commit message.
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->revision->getMessage();
    }

    /**
     * Build the merged representation of the file lines.
     *
     * @return FileInterface[]
     */
    private function buildLines()
    {
        $coverageFileList = array();
        foreach ($this->coverage->getFiles() as $coverageFile) {
            $coverageFileList[$coverageFile->getRelativePath()] = $coverageFile;
        }

        $changedFileList = array();
        foreach ($this->changeset->getFiles() as $changedFile) {
            $changedFileList[$changedFile->getNewFilename()] = $changedFile;
        }

        $mergedFileList = array();
        foreach ($this->rawFileList->getFiles() as $name => $fileLines) {
            $coverageFile = null;
            $changedFile = null;
            $rawLineList = $this->rawFileList->getFile($name);

            if (array_key_exists($name, $coverageFileList)) {
                $coverageFile = $coverageFileList[$name];
            }

            if (array_key_exists($name, $changedFileList)) {
                $changedFile = $changedFileList[$name];
            }

            // Both coverage & diff
            if (!is_null($coverageFile) && !is_null($changedFile)) {
                $mergedFileList[] = new FileCoverageChanged(
                    $coverageFile,
                    $changedFile,
                    $rawLineList
                );

            // Coverage only
            } elseif (!is_null($coverageFile) && is_null($changedFile)) {
                $mergedFileList[] = new FileCoverageUnchanged(
                    $coverageFile,
                    $rawLineList
                );

            // Diff only
            } elseif (is_null($coverageFile) && !is_null($changedFile)) {
                $mergedFileList[] = new FileNoCoverageChanged(
                    $changedFile,
                    $rawLineList
                );

            // Neither coverage nor diff
            } else {
                $mergedFileList[] = new FileNoCoverageUnchanged(
                    $rawLineList,
                    $name
                );
            }
        }

        return $mergedFileList;
    }
}
