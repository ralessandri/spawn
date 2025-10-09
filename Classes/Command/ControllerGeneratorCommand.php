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

use HellYeah\Spawn\ValueObject\ClassName;
use HellYeah\Spawn\ValueObject\ClassNamespace;
use HellYeah\Spawn\ValueObject\ExtensionName;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Command to generate a TYPO3 action controller class.
 */
#[AsCommand(name:'spawn:controller', description:'Creates a new TYPO3 action controller')]
class ControllerGeneratorCommand extends AbstractGeneratorCommand
{
    /**
     * Configures the command with arguments for non-interactive use.
     */
    protected function configure(): void
    {
        $this
            ->addArgument('extension', InputArgument::OPTIONAL, 'The target extension key (e.g., "my_extension")')
            ->addArgument('class', InputArgument::OPTIONAL, 'The controller class name (e.g., "AwesomeController")')
            ->addArgument('namespace', InputArgument::OPTIONAL, 'The namespace prefix (e.g., "Vendor\\Extension\\")');
    }

    /**
     * Gathers and validates inputs for controller generation.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @return array{type: string, extension: ExtensionName, className: ClassName, namespace: ClassNamespace}
     * @throws \HellYeah\Spawn\Exception\AbstractException
     */
    protected function gatherInputs(InputInterface $input): array
    {
        return $this->gatherCommonInputs(
            $input,
            'controller',
            'class',
            'Enter the name of the controller (e.g., "AwesomeController")',
            'AwesomeController'
        );
    }
}
