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
 * Removed line.
 *
 * As removed line have no coverae data we need only the diff line to correctly present a unified representation of this
 *  line type.
 */
class LineRemoved implements LineInterface
{
    /**
     * @var DiffLine
     */
    private $diffLine;


    /**
     * Constructor.
     *
     * @throws \RuntimeException
     *
     * @param DiffLine $diffLine
     */
    public function __construct(DiffLine $diffLine)
    {
        if ($diffLine->getOperation() !== DiffLine::REMOVED) {
            throw new \RuntimeException(
                'Cannot create ' . __CLASS__ . ' with a ' . $diffLine->getOperation() . ' diff line.'
            );
        }

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
        return DiffLine::REMOVED;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return $this->diffLine->getContent();
    }

    /**
     * {@inheritdoc}
     */
    public function getCoverageState()
    {
        return CoverageLine::SKIPPED;
    }
}
