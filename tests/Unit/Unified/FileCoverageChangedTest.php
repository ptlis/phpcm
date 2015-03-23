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
use ptlis\CoverageMonitor\Unified\FileCoverageChanged;
use ptlis\CoverageMonitor\Unified\LineCoverageChanged;
use ptlis\CoverageMonitor\Unified\LineCoverageUnchanged;
use ptlis\CoverageMonitor\Unified\LineNoCoverageChanged;
use ptlis\CoverageMonitor\Unified\LineNoCoverageUnchanged;
use ptlis\DiffParser\File;
use ptlis\DiffParser\Hunk;
use ptlis\DiffParser\Line as DiffLine;

class FileCoverageChangedTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateDiffAddSuccess()
    {
        $file = new FileCoverageChanged(
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
            new File(
                '',
                'src/real.php',
                File::CHANGED,
                array(
                    new Hunk(
                        0,
                        0,
                        0,
                        1,
                        array(
                            new DiffLine(-1, 1, DiffLine::ADDED, '<?php'),
                            new DiffLine(-1, 2, DiffLine::ADDED, '    $foo = $bar ? $baz : $bat;'),
                            new DiffLine(-1, 3, DiffLine::ADDED, '?>')
                        )
                    )
                )
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
            '',
            $file->getOriginalFilename()
        );

        $this->assertEquals(
            'src/real.php',
            $file->getNewFilename()
        );

        $this->assertEquals(
            File::CHANGED,
            $file->getOperation()
        );

        $this->assertEquals(
            array(
                new LineNoCoverageChanged(
                    new DiffLine(-1, 1, DiffLine::ADDED, '<?php')
                ),
                new LineCoverageChanged(
                    new CoverageLine(2, '    $foo = $bar ? $baz : $bat;', 1),
                    new DiffLine(-1, 2, DiffLine::ADDED, '    $foo = $bar ? $baz : $bat;')
                ),
                new LineNoCoverageChanged(
                    new DiffLine(-1, 3, DiffLine::ADDED, '?>')
                ),
                new LineNoCoverageUnchanged(1, 4, ''),
                new LineNoCoverageUnchanged(2, 5, '<h1>test</h1>')
            ),
            $file->getLines()
        );
    }

    public function testCreateDiffRemoveSuccess()
    {
        $file = new FileCoverageChanged(
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
            new File(
                '',
                'src/real.php',
                File::CHANGED,
                array(
                    new Hunk(
                        3,
                        2,
                        3,
                        0,
                        array(
                            new DiffLine(4, -1, DiffLine::REMOVED, ''),
                            new DiffLine(5, -1, DiffLine::REMOVED, '<h1>test</h1>')
                        )
                    )
                )
            ),
            array(
                '<?php',
                '    $foo = $bar ? $baz : $bat;',
                '?>'
            )
        );

        $this->assertEquals(
            '',
            $file->getOriginalFilename()
        );

        $this->assertEquals(
            'src/real.php',
            $file->getNewFilename()
        );

        $this->assertEquals(
            FileCoverageChanged::CHANGED,
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

                new LineNoCoverageChanged(
                    new DiffLine(4, -1, DiffLine::REMOVED, '')
                ),
                new LineNoCoverageChanged(
                    new DiffLine(5, -1, DiffLine::REMOVED, '<h1>test</h1>')
                )
            ),
            $file->getLines()
        );
    }
}
