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
     * @var FileInterface[]
     */
    private $lineList;


    /**
     * Constructor.
     *
     * @param RevisionMeta $revision
     * @param CoverageInterface $coverage
     * @param Changeset $changeset
     */
    public function __construct(
        RevisionMeta $revision,
        CoverageInterface $coverage,
        Changeset $changeset
    ) {
        $this->revision = $revision;
        $this->coverage = $coverage;
        $this->changeset = $changeset;
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
