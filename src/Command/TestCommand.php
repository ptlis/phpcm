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

namespace ptlis\CoverageMonitor\Command;

use ptlis\CoverageMonitor\Coverage\CoverageClover;
use ptlis\CoverageMonitor\Serializer\JsonFilesInRevisionsSerializer;
use ptlis\CoverageMonitor\Serializer\JsonRevisionCoverageSerializer;
use ptlis\CoverageMonitor\Unified\RevisionCoverage;
use ptlis\ShellCommand\ShellCommandBuilder;
use ptlis\ShellCommand\UnixEnvironment;
use ptlis\CoverageMonitor\CommandWrapper\ComposerUpdate;
use ptlis\CoverageMonitor\CommandWrapper\PhpUnit;
use ptlis\Vcs\Git\GitVcs;
use ptlis\Vcs\Shared\CommandExecutor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Simple test command.
 */
class TestCommand extends Command
{
    /**
     * The path to the repository.
     */
    const REPOSITORY_PATH = 'repository-path';

    /**
     * Returned code must be in one of these paths..
     */
    const CODE_PATH_FILTER = 'code-path-filter';


    /**
     * Configure the command metadata.
     */
    public function configure()
    {
        $this
            ->setName('run')
            ->setDescription('Run the command')
            ->addArgument(
                self::REPOSITORY_PATH,
                InputArgument::REQUIRED,
                'The path to the repository'
            )
            ->addOption(
                self::CODE_PATH_FILTER,
                null,
                InputArgument::OPTIONAL,
                'Filter for code paths paths to read from.'
            );
    }

    /**
     * Execute the run command.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return int 0 if everything went fine, or an error code.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $commandBuilder = new ShellCommandBuilder(new UnixEnvironment());

        $composerUpCommand = new ComposerUpdate($commandBuilder);
        $phpUnitCommand = new PhpUnit(
            $commandBuilder,
            realpath(__DIR__ . '/../../vendor/bin/phpunit')
        );

        $workingDirectory = $input->getArgument(self::REPOSITORY_PATH);
        $rawCodePaths = $input->getOption(self::CODE_PATH_FILTER);
        $explodedCodePaths = explode(',', $rawCodePaths);

        $codePathList = array();
        foreach ($explodedCodePaths as $codePath) {
            $codePathList[] = trim($codePath, DIRECTORY_SEPARATOR);
        }


        $vcs = new GitVcs(
            new CommandExecutor(
                $commandBuilder,
                '/usr/bin/git',
                $workingDirectory
            )
        );


        // Setup custom styles
        $errorStyle = new OutputFormatterStyle('white', 'red');
        $output->getFormatter()->setStyle('command-error', $errorStyle);

        $successStyle = new OutputFormatterStyle('white', 'green');
        $output->getFormatter()->setStyle('command-success', $successStyle);




        $meta = $vcs->getMeta();

        $revisionList = $meta->getRevisions();

        $output->writeln('Found ' . count($revisionList) . ' revisions.');


        // Prepare output directory
        $buildDirectory = __DIR__ . '/../../output';
        if (!file_exists($buildDirectory)) {
            mkdir($buildDirectory);
        }

        $count = 0;
        $outputFilenameList = array();
        $revisionCoverageList = array();

        /** @var \ptlis\Vcs\Interfaces\RevisionMetaInterface $revision */
        foreach (array_reverse($revisionList) as $revision) {

            $outputFilenameList[] = array(
                'identifier' => $revision->getIdentifier(),
                'short_identifier' => substr($revision->getIdentifier(), 0, 10),
                'author' => $revision->getAuthor(),
                'created' => $revision->getCreated()->format('c'),
                'message' => $revision->getMessage(),
                'filename' => $revision->getIdentifier() . '.json'
            );

            $count++;

            $output->writeln('#' . str_pad($count, strlen(count($revisionList)), ' ', STR_PAD_LEFT) . ' Revision ' . $revision->getIdentifier());

            $this->writeInitialOutput($output, '    Checking out');
            $vcs->checkoutRevision($revision->getIdentifier());
            $output->write(' <command-success>Done</command-success>', true);



            $this->writeInitialOutput($output, '    Running composer update');
            $composerResult = $composerUpCommand->run($workingDirectory);

            if (0 !== $composerResult->getExitCode()) {
                $output->write(' <command-error>Fail</command-error>', true);

                $vcs->resetRevision();

                throw new \RuntimeException($composerResult->getStdErr(), $composerResult->getExitCode());
            } else {
                $output->write(' <command-success>Done</command-success>', true);
            }


            $coveragePath = tempnam(sys_get_temp_dir(), 'coverage_monitor_');

            $this->writeInitialOutput($output, '    Running PHPUnit');
            $phpUnitResult = $phpUnitCommand->run($workingDirectory, array('--coverage-clover=' . $coveragePath));

            if (0 !== $phpUnitResult->getExitCode()) {
                $output->write(' <command-error>Fail</command-error>', true);
            } else {
                $output->write(' <command-success>Done</command-success>', true);
            }




            $this->writeInitialOutput($output, '    Processing Results');

            try {
                $coverage = new CoverageClover($coveragePath, realpath($workingDirectory));
                $changeset = $meta->getChangeset($revision);

                $revisionCoverage = new RevisionCoverage($revision, $coverage, $changeset);
                $revisionCoverageList[] = $revisionCoverage;

                $serializer = new JsonRevisionCoverageSerializer();

                $file = new \SplFileObject($buildDirectory . '/' . $revision->getIdentifier() . '.json', 'w');
                $file->fwrite($serializer->serialize($revisionCoverage));
                $file->fflush();

                $output->write(' <command-success>Done</command-success>', true);


            } catch (\RuntimeException $e) {
                $output->write(' <command-error>Fail</command-error>', true);
            }





            $vcs->resetRevision();

            $output->writeln('');
        }

        $file = new \SplFileObject($buildDirectory . '/revision_list.json', 'w');
        $file->fwrite(json_encode($outputFilenameList, JSON_PRETTY_PRINT));
        $file->fflush();

        $filesInRevisionsSerializer = new JsonFilesInRevisionsSerializer();
        $file = new \SplFileObject($buildDirectory . '/files_in_revisions.json', 'w');
        $file->fwrite($filesInRevisionsSerializer->serialize($revisionCoverageList));
        $file->fflush();
    }

    private function writeInitialOutput(OutputInterface $output, $text)
    {
        $output->write(
            str_pad($text, 72, '.')
        );
    }
}
