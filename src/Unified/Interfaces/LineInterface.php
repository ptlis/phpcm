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
 * Interface for classes representing a unified (coverage & diff) line.
 */
interface LineInterface
{
    /**
     * Returns the line number in the original file (-1 if new line).
     *
     * @return int
     */
    public function getOriginalLineNo();

    /**
     * Returns the line number in the new file (-1 if deleted line).
     *
     * @return int
     */
    public function getNewLineNo();

    /**
     * Returns the operation that was performed (one of ptlis\DiffParser\Line class constants).
     *
     * @return int
     */
    public function getOperation();

    /**
     * Get the content of the line.
     *
     * @return string
     */
    public function getContent();

    /**
     * Get the number of times this line is covered by tests.
     *
     * @return int
     */
    public function getCoverageCount();

    /**
     * Returns true if this line should be covered, false otherwise.
     *
     * @return bool
     */
    public function shouldHaveCoverage();
}
