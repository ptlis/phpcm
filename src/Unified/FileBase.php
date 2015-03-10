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

use ptlis\CoverageMonitor\Coverage\CoverageFile;
use ptlis\CoverageMonitor\Coverage\CoverageLine;
use ptlis\CoverageMonitor\Unified\Interfaces\FileInterface;
use ptlis\CoverageMonitor\Unified\Interfaces\LineInterface;
use ptlis\DiffParser\File as DiffFile;
use ptlis\DiffParser\Hunk;
use ptlis\DiffParser\Line as DiffLine;

/**
 * Base Line - implements shared logic for Unified file lines.
 */
abstract class FileBase implements FileInterface
{
    /**
     * Internal method to build a unified list of lines.
     *
     * @param CoverageFile $coverageFile
     * @param string[] $rawLineList
     * @param DiffFile|null $diffFile
     *
     * @return LineInterface
     */
    protected function internalGetLines(CoverageFile $coverageFile, array $rawLineList, DiffFile $diffFile = null)
    {
        $unifiedLineList = array();
        $coverageLineList = $coverageFile->getLines();
        $diffLineList = $this->getDiffLines($diffFile);

        // Setup loop variables
        $originalLineNo = 1;
        $newLineNo = 1;
        $originalLastLine = $this->getOriginalLastLineNo($diffFile, $rawLineList);
        $newLastLine = count($rawLineList);

        // Loop until we've reached the final line of both the original & new file
        while ($originalLineNo <= $originalLastLine || $newLineNo <= $newLastLine) {

            $diffLine = $this->getMatchingDiffLine($diffLineList, $originalLineNo, $newLineNo);
            $coverageLine = $this->getMatchingCoverageLine($coverageLineList, $diffLine, $newLineNo);

            $unifiedLineList[] = $this->buildFromLines(
                $coverageLine,
                $diffLine,
                $rawLineList,
                $originalLineNo,
                $newLineNo
            );

            // Conditionally change line numbers depending on line operation.
            if (!is_null($diffLine)) {

                // Added lines do not count towards original line count
                if (DiffLine::ADDED === $diffLine->getOperation()) {
                    $originalLineNo--;

                // Removed lines do not count towards new line count
                // Additionally we should not merge coverage data with this!
                } elseif (DiffLine::REMOVED === $diffLine->getOperation()) {
                    $newLineNo--;
                }
            }

            $originalLineNo++;
            $newLineNo++;
        }

        return $unifiedLineList;
    }

    /**
     * Try to find a diff line for the specified line numbers, return it or null if not found.
     *
     * Note: The number we compare it too depends on the operation.
     *
     * @param DiffLine[] $diffLineList
     * @param int $originalLineNo
     * @param int $newLineNo
     *
     * @return DiffLine|null
     */
    protected function getMatchingDiffLine(
        array &$diffLineList,
        $originalLineNo,
        $newLineNo
    ) {

        $diffLine = null;

        // Diff line, deleted
        if ($this->matchingDeletedLine($diffLineList, $originalLineNo)) {
            $diffLine = array_shift($diffLineList);

        // Diff line, not deleted
        } elseif ($this->matchingNotDeletedLine($diffLineList, $newLineNo)) {
            $diffLine = array_shift($diffLineList);
        }

        return $diffLine;
    }

    /**
     * Returns true if the line was removed and the original line numbers match.
     *
     * @param DiffLine[] $diffLineList
     * @param int $originalLineNo
     *
     * @return bool
     */
    protected function matchingDeletedLine(array $diffLineList, $originalLineNo)
    {
        return count($diffLineList)
            && DiffLine::REMOVED === $diffLineList[0]->getOperation()
            && $diffLineList[0]->getOriginalLineNo() === $originalLineNo;
    }

    /**
     * Returns true if the line was not removed and the new line numbers match.
     *
     * @param DiffLine[] $diffLineList
     * @param int $newLineNo
     *
     * @return bool
     */
    protected function matchingNotDeletedLine(array $diffLineList, $newLineNo)
    {
        return count($diffLineList)
            && DiffLine::REMOVED !== $diffLineList[0]->getOperation()
            && $diffLineList[0]->getNewLineNo() === $newLineNo;
    }

    /**
     * Try to find a coverage line for the specified line number, return it or null if not found.
     *
     * @param CoverageLine[] $coverageLineList
     * @param DiffLine|null $diffLine
     * @param int $lineNo
     *
     * @return CoverageLine|null
     */
    protected function getMatchingCoverageLine(array &$coverageLineList, DiffLine $diffLine = null, $lineNo)
    {
        $coverageLine = null;

        // Diff Line is empty or represents a removed line - try to find coverage
        if (is_null($diffLine) || DiffLine::REMOVED !== $diffLine->getOperation()) {
            if (count($coverageLineList) && $coverageLineList[0]->getLineNo() === $lineNo) {
                $coverageLine = array_shift($coverageLineList);
            }
        }

        return $coverageLine;
    }

    /**
     * Build a unified line out of (optional) coverage & diff lines with raw line data.
     *
     * @param CoverageLine|null $coverageLine
     * @param DiffLine|null $diffLine
     * @param string[] $rawLineList
     * @param int $originalLineNo
     * @param int $newLineNo
     *
     * @return LineCoverageUnchanged|LineNoCoverageUnchanged
     */
    protected function buildFromLines(
        CoverageLine $coverageLine = null,
        DiffLine $diffLine = null,
        array $rawLineList,
        $originalLineNo,
        $newLineNo
    ) {

        // Coverage line only
        if (!is_null($coverageLine) && is_null($diffLine)) {
            $unifiedLine = new LineCoverageUnchanged($coverageLine, $originalLineNo);

        // Diff Line only
        } elseif (is_null($coverageLine) && !is_null($diffLine)) {
            $unifiedLine = new LineNoCoverageChanged($diffLine);

        // Both lines
        } elseif (!is_null($coverageLine) && !is_null($diffLine)) {
            $unifiedLine = new LineCoverageChanged($coverageLine, $diffLine);

        // Neither line
        } else {
            $unifiedLine = new LineNoCoverageUnchanged($originalLineNo, $newLineNo, $rawLineList[$newLineNo - 1]);
        }

        return $unifiedLine;
    }

    /**
     * Get an array of diff lines from diff file.
     *
     * @param DiffFile|null $file
     *
     * @return DiffLine[]
     */
    protected function getDiffLines(DiffFile $file = null)
    {
        $lineList = [];

        if (!is_null($file)) {
            foreach ($file->getHunks() as $hunk) {
                foreach ($hunk->getLines() as $line) {
                    $lineList[] = $line;
                }
            }
        }

        return $lineList;
    }

    /**
     * Determines the original last line for the diff file.
     *
     * @param DiffFile|null $diffFile
     *
     * @return int
     */
    protected function getOriginalLastLineNo(DiffFile $diffFile = null, array $rawLineList)
    {
        if (!is_null($diffFile)) {
            /** @var Hunk $lastHunk */
            $lastHunk = array_pop($diffFile->getHunks());
            $lastLineNo = $lastHunk->getOriginalStart() + $lastHunk->getOriginalCount();
        } else {
            $lastLineNo = count($rawLineList);
        }

        return $lastLineNo;
    }
}
