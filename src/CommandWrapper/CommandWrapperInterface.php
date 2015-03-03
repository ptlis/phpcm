<?php

/**
 * PHP Version 5.5
 *
 * @copyright (c) 2014 Magma Digital Ltd
 * @author brian ridley <brianr@magmadigital.co.uk>
 */

namespace ptlis\CoverageMonitor\CommandWrapper;

use ptlis\ShellCommand\Interfaces\CommandResultInterface;

/**
 * Interface that wrapped commands must implement.
 */
interface CommandWrapperInterface
{
    /**
     * Execute the command & return the result.
     *
     * @param string $workingDirectory
     * @param string[] $arguments
     *
     * @return CommandResultInterface
     */
    public function run($workingDirectory, array $arguments = array());
}
