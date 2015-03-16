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

namespace ptlis\CoverageMonitor\Serializer;

use ptlis\CoverageMonitor\Serializer\Interfaces\FilesInRevisionsSerializerInterface;
use ptlis\CoverageMonitor\Unified\RevisionCoverage;

/**
 * Files in Revisions serializer for JSON format.
 */
class JsonFilesInRevisionsSerializer implements FilesInRevisionsSerializerInterface
{
    /**
     * Serialize information about which files are present in which revisions.
     *
     * @param RevisionCoverage[] $revisionCoverageList
     *
     * @return string
     */
    public function serialize(array $revisionCoverageList)
    {
        $fileList = array();

        foreach ($revisionCoverageList as $revisionCoverage) {
            foreach ($revisionCoverage->getFiles() as $file) {
                if (!array_key_exists($file->getNewFilename(), $fileList)) {
                    $fileList[$file->getNewFilename()] = [];
                }

                $fileList[$file->getNewFilename()][] = $revisionCoverage->getIdentifier();
            }
        }

        return json_encode($fileList);
    }
}
