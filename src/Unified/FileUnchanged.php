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

namespace ptlis\CoverageMonitor\Unified;

use ptlis\CoverageMonitor\Coverage\CoverageFile;
use ptlis\CoverageMonitor\Unified\Interfaces\FileInterface;
use ptlis\CoverageMonitor\Unified\Interfaces\LineInterface;

class FileUnchanged extends FileBase implements FileInterface
{
    /**
     * @var CoverageFile
     */
    private $coverageFile;

    /**
     * @var string[]
     */
    private $fileLineList;


    /**
     * Constructor.
     *
     * @param CoverageFile $coverageFile
     * @param string[] $fileLineList
     */
    public function __construct(CoverageFile $coverageFile, array $fileLineList)
    {
        $this->coverageFile = $coverageFile;
        $this->fileLineList = $fileLineList;
        $this->internalGetLines($this->coverageFile, $this->fileLineList);
    }

    /**
     * {@inheritDoc}
     */
    public function getOriginalFilename()
    {
        return $this->coverageFile->getRelativePath();
    }

    /**
     * {@inheritDoc}
     */
    public function getNewFilename()
    {
        return $this->coverageFile->getRelativePath();
    }

    /**
     * {@inheritDoc}
     */
    public function getOperation()
    {
        return self::UNCHANGED;
    }

    /**
     * {@inheritDoc}
     */
    public function getLines()
    {
        return $this->lineList;
    }
}
