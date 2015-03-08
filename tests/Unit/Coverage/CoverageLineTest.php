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

namespace ptlis\CoverageMonitor\Test\Unit\Coverage;

use ptlis\CoverageMonitor\Coverage\CoverageLine;

class CoverageLineTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $file = new CoverageLine(
            10,
            '   $bar = new Baz();',
            CoverageLine::ERROR
        );

        $this->assertEquals(
            10,
            $file->getLineNo()
        );

        $this->assertEquals(
            '   $bar = new Baz();',
            $file->getContent()
        );

        $this->assertEquals(
            CoverageLine::ERROR,
            $file->getState()
        );
    }
}
