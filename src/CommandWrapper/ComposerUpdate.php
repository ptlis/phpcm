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
 * Wrapper around composer update command building & execution.
 */
class ComposerUpdate implements CommandWrapperInterface
{
    /**
     * @var CommandBuilderInterface
     */
    private $commandBuilder;


    /**
     * Constructor.
     *
     * @param CommandBuilderInterface $commandBuilder
     */
    public function __construct(CommandBuilderInterface $commandBuilder)
    {
        $this->commandBuilder = $commandBuilder;
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
        $command = $this->commandBuilder
            ->setCwd($workingDirectory)
            ->setCommand('composer')
            ->addArguments(array(
                'update'
            ))
            ->buildCommand();

        return $command->runSynchronous();
    }
}
