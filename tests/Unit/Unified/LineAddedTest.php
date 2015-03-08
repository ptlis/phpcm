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
use ptlis\CoverageMonitor\Unified\LineAdded;
use ptlis\DiffParser\Line;

class LineAddedTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateSuccess()
    {
        $addedLine = new LineAdded(
            new CoverageLine(10, '    $foo = "bar";', 1),
            new Line(-1, 10, Line::ADDED, '    $foo = "bar";')
        );

        $this->assertEquals(
            -1,
            $addedLine->getOriginalLineNo()
        );

        $this->assertEquals(
            10,
            $addedLine->getNewLineNo()
        );

        $this->assertEquals(
            Line::ADDED,
            $addedLine->getOperation()
        );

        $this->assertEquals(
            '    $foo = "bar";',
            $addedLine->getContent()
        );

        $this->assertEquals(
            1,
            $addedLine->getCoverageCount()
        );
    }

    public function testCreateNonAddedDiffLine()
    {
        $this->setExpectedException(
            '\RuntimeException',
            'Cannot create ptlis\CoverageMonitor\Unified\LineAdded with a unchanged diff line.'
        );

        new LineAdded(
            new CoverageLine(10, '    $foo = "bar";', 1),
            new Line(10, 10, Line::UNCHANGED, '    $foo = "bar";')
        );
    }
}
