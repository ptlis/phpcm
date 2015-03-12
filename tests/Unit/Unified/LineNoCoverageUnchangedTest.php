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

use ptlis\CoverageMonitor\Unified\LineNoCoverageUnchanged;
use ptlis\DiffParser\Line as DiffLine;

class LineNoCoverageUnchangedTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $line = new LineNoCoverageUnchanged(
            10,
            11,
            '    $foo = "bar";'
        );

        $this->assertEquals(
            10,
            $line->getOriginalLineNo()
        );

        $this->assertEquals(
            11,
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
            0,
            $line->getCoverageCount()
        );

        $this->assertEquals(
            false,
            $line->shouldHaveCoverage()
        );
    }
}
