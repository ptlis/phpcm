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

use ptlis\CoverageMonitor\Coverage\CoverageFile;
use ptlis\CoverageMonitor\Coverage\CoverageLine;
use ptlis\CoverageMonitor\Unified\FileUnchanged;
use ptlis\CoverageMonitor\Unified\LineCoverageUnchanged;
use ptlis\CoverageMonitor\Unified\LineNoCoverageUnchanged;

class FileUnchangedTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateSuccess()
    {
        $file = new FileUnchanged(
            new CoverageFile(
                '/home/bob/package/src/real.php',
                array(
                    new CoverageLine(
                        2,
                        '    $foo = $bar ? $baz : $bat;',
                        1
                    )
                ),
                '/home/bob/package/'
            ),
            array(
                '<?php',
                '    $foo = $bar ? $baz : $bat;',
                '?>',
                '',
                '<h1>test</h1>'
            )
        );

        $this->assertEquals(
            'src/real.php',
            $file->getOriginalFilename()
        );

        $this->assertEquals(
            'src/real.php',
            $file->getNewFilename()
        );

        $this->assertEquals(
            FileUnchanged::UNCHANGED,
            $file->getOperation()
        );

        $this->assertEquals(
            array(
                new LineNoCoverageUnchanged(1, 1, '<?php'),
                new LineCoverageUnchanged(
                    new CoverageLine(2, '    $foo = $bar ? $baz : $bat;', 1),
                    2
                ),
                new LineNoCoverageUnchanged(3, 3, '?>'),
                new LineNoCoverageUnchanged(4, 4, ''),
                new LineNoCoverageUnchanged(5, 5, '<h1>test</h1>')
            ),
            $file->getLines()
        );
    }
}
