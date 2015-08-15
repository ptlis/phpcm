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

namespace ptlis\CoverageMonitor\Config;


class Config
{
    /**
     * @var TestSpecificationConfig[]
     */
    private $testSpecificationList;


    /**
     * Constructor.
     *
     * @param TestSpecificationConfig[] $testSpecificationList
     */
    public function __construct(array $testSpecificationList)
    {
        $this->testSpecificationList = $testSpecificationList;
    }

    /**
     * Get an array containing configurations for test packages to use.
     *
     * @return TestSpecificationConfig[]
     */
    public function getTestSpecifications()
    {
        return $this->testSpecificationList;
    }
}
