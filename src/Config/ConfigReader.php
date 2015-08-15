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

use RomaricDrigon\MetaYaml\Loader\YamlLoader;
use RomaricDrigon\MetaYaml\MetaYaml;

/**
 * Reads phpcm's configuration file into simple value object.
 */
class ConfigReader
{
    /**
     * Attempt to read a configuration file from the specified directory & return a Config object.
     *
     * @throws \LogicException
     *
     * @param string $workingDirectory
     *
     * @return Config
     */
    public function read($workingDirectory)
    {
        $testPackageList = array();

        $loader = new YamlLoader();
        $schema = new MetaYaml(
            $loader->loadFromFile(__DIR__ . '/../Data/schema.yml'),
            true
        );

        $configPath = $workingDirectory . '/.phpcm.yml';

        // Read configuration from file
        if (file_exists($configPath)) {

            // Load & validate config file
            $rawConfig = $loader->loadFromFile($configPath);
            $schema->validate($rawConfig);

            // Process test packages used.
            foreach ($rawConfig['test_packages'] as $index => $testPackageData) {

                if (0 === $index && '<start>' !== $testPackageData['from']) {
                    throw new \LogicException(
                        'Invalid configuration for test suite, the first from field must contain \'start\''
                    );
                }

                $testPackageList[] = new TestSpecificationConfig(
                    $testPackageData['package'],
                    $testPackageData['from'],
                    $testPackageData['version'],
                    $testPackageData['include_path']
                );
            }

        // Default to modern PHPUnit
        } else {
            $testPackageList[] = new TestSpecificationConfig(
                'phpunit',
                '<start>',
                '~4.0',
                false
            );
        }

        return new Config(
            $testPackageList
        );
    }
}
