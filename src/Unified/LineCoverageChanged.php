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
class LineCoverageChanged implements LineInterface
{
    private $coverageLine;
    private $diffLine;

    public function __construct(CoverageLine $coverageLine, DiffLine $diffLine)
    {
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
        return $this->diffLine->getOperation();
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

    /**
     * {@inheritdoc}
     */
    public function shouldHaveCoverage()
    {
        return true;
    }
}
