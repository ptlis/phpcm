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

use ptlis\CoverageMonitor\Serializer\Interfaces\RevisionCoverageSerializerInterface;
use ptlis\CoverageMonitor\Unified\RevisionCoverage;

/**
 * Revision coverage serializer for JSON format.
 */
class JsonRevisionCoverageSerializer implements RevisionCoverageSerializerInterface
{
    /**
     * {@inheritdoc}
     */
    public function serialize(RevisionCoverage $revisionCoverage)
    {
        $revisionData = array(
            'identifier' => $revisionCoverage->getIdentifier(),
            'short_identifier' => substr($revisionCoverage->getIdentifier(), 0, 10),
            'author' => $revisionCoverage->getAuthor(),
            'created' => $revisionCoverage->getCreated()->format('c'),
            'message' => $revisionCoverage->getMessage(),
            'files' => array()
        );

        foreach ($revisionCoverage->getFiles() as $file) {
            $fileData = array(
                'original_filename' => $file->getOriginalFilename(),
                'new_filename' => $file->getNewFilename(),
                'operation' => $file->getOperation(),
                'lines' => array()
            );

            foreach ($file->getLines() as $line) {
                $fileData['lines'][] = array(
                    'original_line_number' => $line->getOriginalLineNo(),
                    'new_line_number' => $line->getNewLineNo(),
                    'operation' => $line->getOperation(),
                    'coverage_count' => $line->getCoverageCount(),
                    'should_have_coverage' => $line->shouldHaveCoverage(),
                    'content' => $line->getContent()
                );
            }

            $revisionData['files'][] = $fileData;
        }

        return json_encode($revisionData);
    }
}
