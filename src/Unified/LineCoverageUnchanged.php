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
 * @todo 'Covered' is wrong term for this.
 */
class LineCoverageUnchanged implements LineInterface
{
    /**
     * @var CoverageLine
     */
    private $coverageLine;

    /**
     * @var int
     */
    private $originalLineNo;


    /**
     * Constructor.
     *
     * @param CoverageLine $coverageLine
     * @param int $originalLineNo
     */
    public function __construct(CoverageLine $coverageLine, $originalLineNo)
    {
        $this->coverageLine = $coverageLine;
        $this->originalLineNo = $originalLineNo;
    }

    /**
     * {@inheritdoc}
     */
    public function getOriginalLineNo()
    {
        return $this->originalLineNo;
    }

    /**
     * {@inheritdoc}
     */
    public function getNewLineNo()
    {
        return $this->coverageLine->getLineNo();
    }

    /**
     * {@inheritdoc}
     */
    public function getOperation()
    {
        return DiffLine::UNCHANGED;
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
