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

namespace ptlis\CoverageMonitor\Coverage\Interfaces;

use ptlis\CoverageMonitor\Coverage\CoverageFile;

/**
 * Interface for accessing coverage data from different formats.
 */
interface CoverageInterface
{
    /**
     * Return the coverage files.
     *
     * @return CoverageFile[]
     */
    public function getFiles();
}
