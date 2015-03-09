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

namespace ptlis\CoverageMonitor\Test\Unit\Unified;

use ptlis\CoverageMonitor\Coverage\CoverageLine;
use ptlis\CoverageMonitor\Unified\LineCoverageUnchanged;
use ptlis\DiffParser\Line as DiffLine;

class LineCoverageUnchangedTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $line = new LineCoverageUnchanged(
            new CoverageLine(10, '    $foo = "bar";', 1),
            10
        );

        $this->assertEquals(
            10,
            $line->getOriginalLineNo()
        );

        $this->assertEquals(
            10,
            $line->getNewLineNo()
        );

        $this->assertEquals(
            DiffLine::UNCHANGED,
            $line->getOperation()
        );

        $this->assertEquals(
            '    $foo = "bar";',
            $line->getContent()
        );

        $this->assertEquals(
            1,
            $line->getCoverageCount()
        );
    }
}
