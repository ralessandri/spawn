<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace HellYeah\Spawn\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Command to generate a TYPO3 CLI command class.
 */
#[AsCommand(name: 'spawn:command', description: 'Creates a new TYPO3 CLI command')]
class CommandGeneratorCommand extends AbstractGeneratorCommand
{
    /**
     * Configures the command with arguments for non-interactive use.
     */
    protected function configure(): void
    {
        $this
            ->addArgument('extension', InputArgument::OPTIONAL, 'The target extension key (e.g., "awesome_extension")')
            ->addArgument('class', InputArgument::OPTIONAL, 'The command class name (e.g., "AwesomeCommand")')
            ->addArgument('namespace', InputArgument::OPTIONAL, 'The namespace prefix (e.g., "Vendor\\Extension\\")')
            ->addArgument('command-name', InputArgument::OPTIONAL, 'The command name (e.g., "awesome_extension:awesome")')
            ->addArgument('command-description', InputArgument::OPTIONAL, 'The command description (e.g., "Executes awesome action")');
    }

    /**
     * Gathers and validates inputs for command generation.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @return array
     * @throws \HellYeah\Spawn\Exception\AbstractException
     * @throws \TYPO3\CMS\Core\Package\Exception\UnknownPackageException
     */
    protected function gatherInputs(InputInterface $input): array
    {
        return $this->gatherCommonInputs(
            $input,
            'command',
            'class',
            'Enter the name of the command (e.g., "AwesomeCommand")',
            'AwesomeCommand'
        );
    }
}
