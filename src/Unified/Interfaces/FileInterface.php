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

namespace ptlis\CoverageMonitor\Unified\Interfaces;

/**
 * Interface for classes representing a unified (coverage & diff) file.
 */
interface FileInterface
{
    const CHANGED = 'changed';
    const UNCHANGED = 'unchanged';

    /**
     * Get the original name of the file.
     *
     * @return string
     */
    public function getOriginalFilename();

    /**
     * Get the new name of the file.
     *
     * @return string
     */
    public function getNewFilename();

    /**
     * Get the operation performed on the file (one of class constants).
     *
     * @return string
     */
    public function getOperation();

    /**
     * Get the contents of the file line-by-line.
     *
     * @return LineInterface[]
     */
    public function getLines();

    /**
     * Get coverage metrics about the file.
     *
     * @return array
     */
    public function getMetrics();
}
