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

use ptlis\CoverageMonitor\Unified\Interfaces\FileInterface;
use ptlis\DiffParser\File as DiffFile;

class FileNoCoverageChanged extends FileBase implements FileInterface
{
    /**
     * @var DiffFile
     */
    private $diffFile;

    /**
     * @var string[]
     */
    private $fileLineList;


    /**
     * Constructor.
     *
     * @param DiffFile $diffFile
     * @param string[] $fileLineList
     */
    public function __construct(DiffFile $diffFile, array $fileLineList)
    {
        $this->diffFile = $diffFile;
        $this->fileLineList = $fileLineList;
        $this->internalGetLines($this->fileLineList, null, $this->diffFile);
    }

    /**
     * {@inheritDoc}
     */
    public function getOriginalFilename()
    {
        return $this->diffFile->getOriginalFilename();
    }

    /**
     * {@inheritDoc}
     */
    public function getNewFilename()
    {
        return $this->diffFile->getNewFilename();
    }

    /**
     * {@inheritDoc}
     */
    public function getOperation()
    {
        return self::CHANGED;
    }

    /**
     * {@inheritDoc}
     */
    public function getLines()
    {
        return $this->lineList;
    }
}
