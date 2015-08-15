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

namespace ptlis\CoverageMonitor\Packages;

use ptlis\SemanticVersion\Version\VersionInterface;

/**
 * Value object bundling a test suite & version number.
 */
class TestSpecification
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var VersionInterface
     */
    private $version;

    /**
     * @var string
     */
    private $downloadMethod;


    /**
     * Construct.
     *
     * @param string $name
     * @param VersionInterface $version
     * @param string $downloadMethod
     */
    public function __construct($name, VersionInterface $version, $downloadMethod)
    {
        $this->name = $name;
        $this->version = $version;
        $this->downloadMethod = $downloadMethod;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return VersionInterface
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return string
     */
    public function getDownloadMethod()
    {
        return $this->downloadMethod;
    }
}
