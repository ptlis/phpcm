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

use ptlis\CoverageMonitor\Config\Config;
use ptlis\CoverageMonitor\Config\TestSpecificationConfig;
use ptlis\SemanticVersion\Comparator\GreaterThan;
use ptlis\SemanticVersion\Version\VersionInterface;
use ptlis\SemanticVersion\VersionEngine;
use ptlis\SemanticVersion\VersionRange\VersionRangeInterface;
use ptlis\Vcs\Interfaces\RevisionMetaInterface;

/**
 * Attempt to reduce the number of packages down to the minimum installable number.
 */
class BuildTestSpecifications
{
    /**
     * Gets a full list of revisions mapped to test specifications (if matching one is found) or null.
     *
     * @param TestSpecification[] $testSpecificationList
     * @param RevisionMetaInterface[] $revisionList
     *
     * @return TestSpecification[] With revision identifier as the key.
     */
    public function getRevisionTestSpecifications(array $testSpecificationList, array $revisionList)
    {
        $currentSpec = null;
        if (array_key_exists('<start>', $testSpecificationList)) {
            $currentSpec = $testSpecificationList['<start>'];
        }

        $revisionToVersionList = array();
        foreach ($revisionList as $revision) {
            if (array_key_exists($revision->getIdentifier(), $testSpecificationList)) {
                $currentSpec = $testSpecificationList[$revision->getIdentifier()];
            }

            $revisionToVersionList[$revision->getIdentifier()] = $currentSpec;
        }

        return $revisionToVersionList;
    }

    /**
     * Get a list of packages & versions to install.
     *
     * @param Config $config
     *
     * @return TestSpecification[] Key is the revision from which this test package applies.
     */
    public function getTestSpecifications(Config $config)
    {
        // First get all possible versions matching each item in our package list
        $allPackageVersions = $this->getAllPossibleVersions($config->getTestSpecifications());

        $matchingVersionList = $this->getMatchingVersionLists(
            $config,
            $allPackageVersions
        );

        // Final version list
        $testSpecificationList = $this->coalesceVersions($matchingVersionList);

        return $testSpecificationList;
    }

    /**
     * Iterates over the package & version list and returns
     *
     * @param array $matchingVersionList
     *
     * @return TestSpecification[] Keys are the revision from which the package applies
     */
    private function coalesceVersions(array $matchingVersionList)
    {
        $coalescedVersionList = array();

        // In theory we're moving forwards through time here - this simple solution may suffice
        foreach ($matchingVersionList as $packageName => $revisionVersionList) {

            $coalescedVersionList += $this->coalesceVersionList(
                $packageName,
                $revisionVersionList
            );
        }

        return $coalescedVersionList;
    }

    /**
     * Find a (hopefully) set of packages that fulfills the version requirements.
     *
     * @param string $packageName
     * @param array $multipleVersionList
     *
     * @return TestSpecification[] Keys are the revision from which the package applies
     */
    public function coalesceVersionList($packageName, array $multipleVersionList)
    {
        $newVersionList = array();

        $versionAccumulator = array();
        $identifierAccumulator = array();
        foreach ($multipleVersionList as $revisionIdentifier => $versionList) {

            $intersectList = array_intersect($versionAccumulator, $versionList);

            // No intersection, write to version list & reset the accumulators to the current values
            if (!count($intersectList)) {
                $newVersionList += $this->mapIdentifiersToVersions($packageName, $versionAccumulator, $identifierAccumulator);

                $versionAccumulator = $versionList;
                $identifierAccumulator = array($revisionIdentifier);

            // Intersection, store new list & add revision to accumulator.
            } else {
                $versionAccumulator = $intersectList;
                $identifierAccumulator[] = $revisionIdentifier;
            }
        }

        // Handle any remaining versions at the end of the loop
        $newVersionList += $this->mapIdentifiersToVersions($packageName, $versionAccumulator, $identifierAccumulator);

        return $newVersionList;
    }


    /**
     * Returns an array where the key is the revision identifier and the value is a version number.
     *
     * @param string $packageName
     * @param VersionInterface[] $versionList
     * @param string[] $identifierList
     *
     * @return TestSpecification[] Keys are the revision from which the package applies.
     */
    private function mapIdentifiersToVersions($packageName, array $versionList, array $identifierList)
    {
        $identifierToVersionList = array();

        $largestVersion = $this->getLargestVersion($versionList);
        foreach ($identifierList as $identifier) {
            $identifierToVersionList[$identifier] = new TestSpecification(
                $packageName,
                $largestVersion,
                'packagist'     // TODO: Don't hardcode!
            );
        }

        return $identifierToVersionList;
    }


    /**
     * Return the largest revision from the array.
     *
     * @param VersionInterface[] $versionList
     *
     * @return VersionInterface|null
     */
    private function getLargestVersion($versionList)
    {
        $greaterThan = new GreaterThan();

        return array_reduce(
            $versionList,
            function($carryValue, $newValue) use ($greaterThan) {
                if (is_null($carryValue) || $greaterThan->compare($newValue, $carryValue)) {
                    return $newValue;

                } else {
                    return $carryValue;
                }
            }
        );
    }

    /**
     *
     * @throws InvalidBoundingPairException
     *
     * @param Config $config
     * @param VersionInterface[] $allPackageVersions
     *
     * @return array Multi-dimensional array, first level is test packages, second is project revisions and contents
     *  is an array of versions matching the specified version range.
     */
    private function getMatchingVersionLists(Config $config, array $allPackageVersions)
    {
        $versionEngine = new VersionEngine();

        // First pass, get possible matching versions
        $matchingVersionList = array();
        foreach ($config->getTestSpecifications() as $testSpecification) {

            $packageName = $testSpecification->getName();

            if (array_key_exists($packageName, $allPackageVersions)) {

                // Default empty array for this package
                if (!array_key_exists($packageName, $matchingVersionList)) {
                    $matchingVersionList[$packageName] = array();
                }

                $targetVersion = $versionEngine->parseRange($testSpecification->getVersion());

                $versionList = array();
                foreach ($allPackageVersions[$packageName] as $version) {
                    if ($this->isAcceptableVersion($targetVersion, $version)) {
                        $versionList[] = $version;
                    }
                }

                $matchingVersionList[$packageName][$testSpecification->getFromRevision()] = $versionList;
            }
        }

        return $matchingVersionList;
    }

    /**
     * Read package versions from packagist.
     *
     * @param TestSpecificationConfig[] $testPackageList
     *
     * @return array key is the package name, value is all possible matching versions.
     */
    private function getAllPossibleVersions(array $testPackageList)
    {
        $packageVersionList = array();

        $packagistReader = new PackagistReader();
        foreach ($testPackageList as $testPackage) {
            $packageName = $testPackage->getName();
            if (!array_key_exists($packageName, $packageVersionList)) {
                $packageVersionList[$packageName] = $packagistReader->readPackageVersions($packageName);
            }
        }

        return $packageVersionList;
    }

    /**
     * Returns true if the version provided satisfies the requirements of the target version range.
     *
     * @param VersionRangeInterface $targetVersionRange
     * @param VersionInterface $version
     *
     * @return bool
     */
    private function isAcceptableVersion(VersionRangeInterface $targetVersionRange, VersionInterface $version)
    {
        return $targetVersionRange->isSatisfiedBy($version)
            && is_numeric($version->getMajor())
            && is_numeric($version->getMinor())
            && is_numeric($version->getPatch());
    }
}

