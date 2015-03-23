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

class FileNoCoverageUnchanged extends FileBase implements FileInterface
{
    /**
     * @var string[]
     */
    private $fileLineList;

    /**
     * @var string
     */
    private $filename;


    /**
     * Constructor.
     *
     * @param string[] $fileLineList
     * @param string $filename
     */
    public function __construct(array $fileLineList, $filename)
    {
        $this->fileLineList = $fileLineList;
        $this->filename = $filename;
        $this->internalGetLines($this->fileLineList);
    }

    /**
     * {@inheritDoc}
     */
    public function getOriginalFilename()
    {
        return $this->filename;
    }

    /**
     * {@inheritDoc}
     */
    public function getNewFilename()
    {
        return $this->filename;
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
