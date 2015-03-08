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

namespace ptlis\CoverageMonitor\Coverage;

/**
 * Class representing a signle line in a source file.
 */
class CoverageLine
{    /**
     * @var int
     */
    private $lineNo;

    /**
     * @var string
     */
    private $content;

    /**
     * @var int Number of times covered by tests.
     */
    private $count;


    /**
     * Constructor
     *
     * @param int $lineNo
     * @param string $content
     * @param int $count
     */
    public function __construct($lineNo, $content, $count)
    {
        $this->lineNo = $lineNo;
        $this->content = $content;
        $this->count = $count;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return int
     */
    public function getLineNo()
    {
        return $this->lineNo;
    }

    /**
     * Get the number of times this line was covered by tests.
     *
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }
}
