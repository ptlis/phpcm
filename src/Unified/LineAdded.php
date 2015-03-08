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

use ptlis\CoverageMonitor\Coverage\CoverageLine;
use ptlis\CoverageMonitor\Unified\Interfaces\LineInterface;
use ptlis\DiffParser\Line as DiffLine;

/**
 * Added line.
 *
 * We need the diff & coverage lines to correctly present a unified representation of this line type.
 */
class LineAdded implements LineInterface
{
    /**
     * @var CoverageLine
     */
    private $coverageLine;

    /**
     * @var DiffLine
     */
    private $diffLine;


    /**
     * Constructor.
     *
     * @throws \RuntimeException
     *
     * @param CoverageLine $coverageLine
     * @param DiffLine $diffLine
     */
    public function __construct(CoverageLine $coverageLine, DiffLine $diffLine)
    {
        if ($diffLine->getOperation() !== DiffLine::ADDED) {
            throw new \RuntimeException(
                'Cannot create ' . __CLASS__ . ' with a ' . $diffLine->getOperation() . ' diff line.'
            );
        }

        $this->coverageLine = $coverageLine;
        $this->diffLine = $diffLine;
    }

    /**
     * {@inheritdoc}
     */
    public function getOriginalLineNo()
    {
        return $this->diffLine->getOriginalLineNo();
    }

    /**
     * {@inheritdoc}
     */
    public function getNewLineNo()
    {
        return $this->diffLine->getNewLineNo();
    }

    /**
     * {@inheritdoc}
     */
    public function getOperation()
    {
        return DiffLine::ADDED;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return $this->coverageLine->getContent();
    }

    /**
     * {@inheritdoc}
     */
    public function getCoverageCount()
    {
        return $this->coverageLine->getCount();
    }
}
