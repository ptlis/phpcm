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

use ptlis\CoverageMonitor\Coverage\CoverageClover;

class CoverageCloverTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateSuccessNoNamespace()
    {
        $coverage = new CoverageClover(
            realpath(__DIR__ . '/../../data/fake_project_001/coverage.clover'),
            realpath(__DIR__ . '/../../data/fake_project_001')
        );

        $coverageFiles = $coverage->getFiles();

        $this->assertEquals(
            1,
            count($coverageFiles)
        );

        $this->assertEquals(
            8,
            count($coverageFiles[0]->getLines())
        );
    }

    public function testCreateSuccessNamespace()
    {
        $coverage = new CoverageClover(
            realpath(__DIR__ . '/../../data/fake_project_002/coverage.clover'),
            realpath(__DIR__ . '/../../data/fake_project_002')
        );

        $coverageFiles = $coverage->getFiles();

        $this->assertEquals(
            2,
            count($coverageFiles)
        );

        $this->assertEquals(
            8,
            count($coverageFiles[0]->getLines())
        );

        $this->assertEquals(
            8,
            count($coverageFiles[1]->getLines())
        );
    }

    public function testCreateErrorInvalidCloverPath()
    {
        $pathToCoverage = realpath(__DIR__ . '/../../data/fake_project_001/foo.bar');

        $this->setExpectedException(
            '\RuntimeException',
            'Provided path "' . $pathToCoverage . '" does not point to a valid XML document.'
        );

        new CoverageClover(
            $pathToCoverage,
            realpath(__DIR__ . '/../../data/fake_project_001')
        );
    }
}
