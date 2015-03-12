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

use ptlis\CoverageMonitor\Unified\Interfaces\LineInterface;
use ptlis\DiffParser\Line as DiffLine;

/**
 * @todo 'Covered' is wrong term for this.
 */
class LineNoCoverageUnchanged implements LineInterface
{
    /**
     * @var int
     */
    private $originalLineNo;

    /**
     * @var int
     */
    private $newLineNo;

    /**
     * @var string
     */
    private $content;


    /**
     * Constructor.
     *
     * @param int $originalLineNo
     * @param int $newLineNo
     * @param string $content
     */
    public function __construct($originalLineNo, $newLineNo, $content)
    {
        $this->originalLineNo = $originalLineNo;
        $this->newLineNo = $newLineNo;
        $this->content = $content;
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
        return $this->newLineNo;
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
        return $this->content;
    }

    /**
     * {@inheritdoc}
     */
    public function getCoverageCount()
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function shouldHaveCoverage()
    {
        return false;
    }
}
