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

/**
 * Simple value object storing a test specification configuration.
 */
class TestSpecificationConfig
{
    /**
     * @var string
     */
    private $packageName;

    /**
     * @var string
     */
    private $fromRevision;

    /**
     * @var string
     */
    private $version;

    /**
     * @var bool
     */
    private $includePath;


    /**
     * Constructor.
     *
     * @param string $packageName
     * @param string $fromRevision
     * @param string $version
     * @param bool $includePath
     */
    public function __construct(
        $packageName,
        $fromRevision,
        $version,
        $includePath
    ) {
        $this->packageName = $packageName;
        $this->fromRevision = $fromRevision;
        $this->version = $version;
        $this->includePath = $includePath;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->packageName;
    }

    /**
     * @return string
     */
    public function getFromRevision()
    {
        return $this->fromRevision;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return boolean
     */
    public function isIncludePath()
    {
        return $this->includePath;
    }
}
