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
use ptlis\CoverageMonitor\Unified\LineRemoved;
use ptlis\DiffParser\Line;

class LineRemovedTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateSuccess()
    {
        $removedLine = new LineRemoved(
            new Line(10, -1, Line::REMOVED, '')
        );

        $this->assertEquals(
            10,
            $removedLine->getOriginalLineNo()
        );

        $this->assertEquals(
            -1,
            $removedLine->getNewLineNo()
        );

        $this->assertEquals(
            Line::REMOVED,
            $removedLine->getOperation()
        );

        $this->assertEquals(
            '',
            $removedLine->getContent()
        );

        $this->assertEquals(
            0,
            $removedLine->getCoverageCount()
        );
    }

    public function testCreateNonRemovedDiffLine()
    {
        $this->setExpectedException(
            '\RuntimeException',
            'Cannot create ptlis\CoverageMonitor\Unified\LineRemoved with a unchanged diff line.'
        );

        new LineRemoved(
            new Line(10, 10, Line::UNCHANGED, '    $foo = "bar";')
        );
    }
}
