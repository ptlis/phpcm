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


use GuzzleHttp\Client;
use ptlis\SemanticVersion\Exception\InvalidVersionException;
use ptlis\SemanticVersion\Version\VersionInterface;
use ptlis\SemanticVersion\VersionEngine;

class PackagistReader
{
    /**
     * Read package versions from Packagist & return an array of objects representing valid versions.
     *
     * @param string $packageName
     *
     * @return VersionInterface[]
     */
    public function readPackageVersions($packageName)
    {
        $client = new Client();
        $response = $client->get('https://packagist.org/p/' . $packageName . '.json');

        $versionEngine = new VersionEngine();

        if (200 === $response->getStatusCode()) {
            $jsonData = $response->json();

            $versionList = array();

            if (array_key_exists('packages', $jsonData)) {
                foreach ($jsonData['packages'] as $packageData) {
                    foreach ($packageData as $release) {
                        if (array_key_exists('version', $release)) {

                            try {
                                $versionEngine->parseRange($release['version']);
                                $version = $versionEngine->parseVersion($release['version']);
                                $versionList[] = $version;

                            } catch (\InvalidArgumentException $e) {
                                // Ignore invalid versions

                            } catch (\RuntimeException $e) {
                                // Ignore invalid versions
                            }
                        }
                    }
                }

            }

        } else {
            throw new \RuntimeException('Package named "' . $packageName . '" not found."');
        }

        return $versionList;
    }
}
