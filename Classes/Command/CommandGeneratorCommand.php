<?php

declare(strict_types=1);

namespace HellYeah\Spawn\Command;

use HellYeah\Spawn\ValueObject\ClassName;
use HellYeah\Spawn\ValueObject\ClassNamespace;
use HellYeah\Spawn\ValueObject\ExtensionName;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Command to generate a TYPO3 CLI command class.
 */
#[AsCommand(name:'spawn:command', description:'Creates a new TYPO3 CLI command')]
class CommandGeneratorCommand extends AbstractGeneratorCommand
{

    /**
     * Configures the command with arguments for non-interactive use.
     */
    protected function configure(): void
    {
        $this
            ->addArgument('extension', InputArgument::OPTIONAL, 'The target extension key (e.g., "my_extension")')
            ->addArgument('class', InputArgument::OPTIONAL, 'The command class name (e.g., "AwesomeCommand")')
            ->addArgument('namespace', InputArgument::OPTIONAL, 'The namespace prefix (e.g., "Vendor\\Extension\\")')
            ->addArgument('command-name', InputArgument::OPTIONAL, 'The command name (e.g., "myext:awesome")')
            ->addArgument('command-description', InputArgument::OPTIONAL, 'The command description (e.g., "Executes awesome action")');
    }

    /**
     * Gathers and validates inputs for command generation.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @return array{type: string, extension: ExtensionName, className: ClassName, namespace: ClassNamespace, attributes?: array<string, CommandAttribute>}
     * @throws \HellYeah\Spawn\Exception\AbstractException
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
