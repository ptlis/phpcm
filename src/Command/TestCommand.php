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
use ptlis\CoverageMonitor\Unified\FileChanged;
use ptlis\CoverageMonitor\Unified\FileUnchanged;
use ptlis\ShellCommand\ShellCommandBuilder;
use ptlis\ShellCommand\UnixEnvironment;
use ptlis\CoverageMonitor\CommandWrapper\ComposerUpdate;
use ptlis\CoverageMonitor\CommandWrapper\PhpUnit;
use ptlis\CoverageMonitor\Coverage\CoverageDirectory;
use ptlis\CoverageMonitor\Coverage\CoverageFile;
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
     * Configure the command metadata.
     */
    public function configure()
    {
        $this
            ->setName('run')
            ->setDescription('Run the command')
            ->addArgument(
                'path',
                InputArgument::REQUIRED,
                'The path to the repository'
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

        $workingDirectory = $input->getArgument('path');

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

        $count = 0;

        $coverageData = array();

        /** @var \ptlis\Vcs\Interfaces\RevisionMetaInterface $revision */
        foreach (array_reverse($revisionList) as $revision) {
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

                $output->write(' <command-success>Done</command-success>', true);
            } catch (\RuntimeException $e) {
                $output->write(' <command-error>Fail</command-error>', true);
            }


            $changeset = $meta->getChangeset($revision);


            echo PHP_EOL . PHP_EOL;

            $pairings = array();
            foreach ($coverage->getFiles() as $coverageFile) {
                $found = false;
                $rawFileLineList = file($coverageFile->getFullPath(), FILE_IGNORE_NEW_LINES);

                foreach ($changeset->getFiles() as $changedFile) {
                    if ($changedFile->getNewFilename() === $coverageFile->getRelativePath()) {
                        $pairings[] = new FileChanged(
                            $coverageFile,
                            $changedFile,
                            $rawFileLineList
                        );
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    $pairings[] = new FileUnchanged(
                        $coverageFile,
                        $rawFileLineList
                    );
                }
            }

            $vcs->resetRevision();
        }
    }

    private function writeInitialOutput(OutputInterface $output, $text)
    {
        $output->write(
            str_pad($text, 72, '.')
        );
    }
}
