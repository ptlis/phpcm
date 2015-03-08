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

use ptlis\CoverageMonitor\Coverage\CoverageFile;
use ptlis\CoverageMonitor\Coverage\CoverageLine;

class CoverageFileTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateSuccess()
    {
        $coverageLines = array(
            new CoverageLine(
                5,
                '$baz = "bat";',
                1
            )
        );

        $file = new CoverageFile(
            '/foo/bar/baz/src/foo.php',
            $coverageLines,
            '/foo/bar/baz'
        );

        $this->assertEquals(
            'foo.php',
            $file->getName()
        );

        $this->assertEquals(
            '/foo/bar/baz/src/foo.php',
            $file->getFullPath()
        );

        $this->assertEquals(
            'src/foo.php',
            $file->getRelativePath()
        );

        $this->assertEquals(
            $coverageLines,
            $file->getLines()
        );
    }

    public function testCreateErrorInvalidWorkingDirectory()
    {
        $this->setExpectedException(
            '\RuntimeException',
            'Incorrect working directory provided.'
        );

        new CoverageFile(
            '/baz/bat/src/foo.php',
            array(),
            '/foo/bar/baz'
        );
    }
}
