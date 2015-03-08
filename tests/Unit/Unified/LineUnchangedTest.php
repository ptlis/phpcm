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
use ptlis\CoverageMonitor\Unified\LineUnchanged;
use ptlis\DiffParser\Line;

class LineUnchangedTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateSuccess()
    {
        $addedLine = new LineUnchanged(
            new CoverageLine(10, '    $foo = "bar";', 2),
            10
        );

        $this->assertEquals(
            10,
            $addedLine->getOriginalLineNo()
        );

        $this->assertEquals(
            10,
            $addedLine->getNewLineNo()
        );

        $this->assertEquals(
            Line::UNCHANGED,
            $addedLine->getOperation()
        );

        $this->assertEquals(
            '    $foo = "bar";',
            $addedLine->getContent()
        );

        $this->assertEquals(
            2,
            $addedLine->getCoverageCount()
        );
    }
}
