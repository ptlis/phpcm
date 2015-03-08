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

namespace ptlis\CoverageMonitor\CommandWrapper;

use ptlis\ShellCommand\Interfaces\CommandBuilderInterface;
use ptlis\ShellCommand\Interfaces\CommandResultInterface;

/**
 * Wrapper around running PHPUnit tests.
 */
class PhpUnit implements CommandWrapperInterface
{
    /**
     * @var CommandBuilderInterface
     */
    private $commandBuilder;

    /**
     * @var string Path to PhpCM's phpunit command.
     */
    private $pathToPhpUnit;


    /**
     * Constructor.
     *
     * @param CommandBuilderInterface $commandBuilder
     * @param string $pathToPhpUnit
     */
    public function __construct(CommandBuilderInterface $commandBuilder, $pathToPhpUnit)
    {
        $this->commandBuilder = $commandBuilder;
        $this->pathToPhpUnit = $pathToPhpUnit;
    }

    /**
     * Execute the command & return the result.
     *
     * @param string $workingDirectory
     * @param string[] $arguments
     *
     * @return CommandResultInterface
     */
    public function run($workingDirectory, array $arguments = array())
    {
        $phpUnitPath = $this->pathToPhpUnit;

        $command = $this->commandBuilder
            ->setCwd($workingDirectory)
            ->setCommand($phpUnitPath)
            ->addArguments($arguments)
            ->buildCommand();

        return $command->runSynchronous();
    }
}
