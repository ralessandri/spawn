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

use HellYeah\Spawn\Event\PostGenerateEvent;
use HellYeah\Spawn\Event\PreGenerateEvent;
use HellYeah\Spawn\Service\ExtensionService;
use HellYeah\Spawn\Service\FileWriterService;
use HellYeah\Spawn\Service\GeneratorRegistryService;
use HellYeah\Spawn\Service\InputValidatorService;
use HellYeah\Spawn\Service\PhpParserService;
use HellYeah\Spawn\Service\TemplateLoaderService;
use HellYeah\Spawn\ValueObject\ClassName;
use HellYeah\Spawn\ValueObject\ClassNamespace;
use HellYeah\Spawn\ValueObject\CommandAttribute;
use HellYeah\Spawn\ValueObject\ExtensionName;
use HellYeah\Spawn\ValueObject\NamespacePrefix;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Abstract base class for generation commands, providing common IO, service injection, and hooks.
 */
abstract class AbstractGeneratorCommand extends Command
{
    protected SymfonyStyle $io;

    public function __construct(
        protected readonly ExtensionService $extensionService,
        protected readonly TemplateLoaderService $templateLoaderService,
        protected readonly PhpParserService $phpParserService,
        protected readonly FileWriterService $fileWriterService,
        protected readonly InputValidatorService $inputValidatorService,
        protected readonly GeneratorRegistryService $generatorRegistry,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
        parent::__construct();
    }

    /**
     * Initializes IO for interactive prompts and output.
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    /**
     * Gathers and validates user inputs based on the generation type.
     *
     * @return array Validated input data (e.g., extension, class name, namespace).
     */
    abstract protected function gatherInputs(InputInterface $input): array;

    /**
     * Executes the command: gathers inputs, processes template, writes file, and dispatches events.
     *
     * @throws \HellYeah\Spawn\Exception\AbstractException
     * @throws \TYPO3\CMS\Core\Package\Exception\UnknownPackageException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $inputs = $this->gatherInputs($input);

        // Dispatch pre-generate event
        $this->eventDispatcher->dispatch(new PreGenerateEvent($inputs));

        $templateCode = $this->processTemplate($inputs);
        $targetPath = $this->getTargetPath($inputs);

        $this->fileWriterService->write($targetPath, $templateCode);

        // Dispatch post-generate event
        $this->eventDispatcher->dispatch(new PostGenerateEvent($inputs, $targetPath));

        $this->io->success('Successfully generated the requested file.');

        return Command::SUCCESS;
    }

    /**
     * Processes the template
     *
     * @param array $inputs Validated input data.
     *
     * @return string Processed PHP code.
     * @throws \HellYeah\Spawn\Exception\AbstractException
     */
    protected function processTemplate(array $inputs): string
    {
        $config = $this->generatorRegistry->getConfig($inputs['type']);
        $templateCode = $this->templateLoaderService->load($config['templatePath']);

        $parser = $this->phpParserService->setCode($templateCode);

        // Handle command-specific attributes if present
        if ($inputs['type'] === 'command' && isset($inputs['attributes']['commandName'], $inputs['attributes']['commandDescription'])) {
            $parser->setCommandAttribute(
                $inputs['attributes']['commandName']->getValue(),
                $inputs['attributes']['commandDescription']->getValue()
            );
        }

        return $parser
            ->setNamespace($inputs['namespace']->getValue())
            ->setClassname($inputs['className']->getValue())
            ->getPhpCode();
    }

    /**
     * Determines the target file path using the registry and inputs.
     *
     * @param array $inputs Validated input data.
     *
     * @return string
     * @throws \HellYeah\Spawn\Exception\AbstractException
     * @throws \TYPO3\CMS\Core\Package\Exception\UnknownPackageException
     */
    protected function getTargetPath(array $inputs): string
    {
        $config = $this->generatorRegistry->getConfig($inputs['type']);

        return $this->extensionService->getExtensionPath($inputs['extension'])
               . $config['targetDir']
               . $inputs['className']->getValue() . '.php';
    }

    /**
     * Gathers common inputs for class generation (extension, class name, namespace, optional attributes).
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param string $type The generation type (e.g., 'controller', 'command').
     * @param string $classArgumentName The input argument name for the class (e.g., 'controller').
     * @param string $askPrompt The prompt for interactive class name input.
     * @param string $defaultName The default class name for interactive input.
     *
     * @return array{type: string, extension: ExtensionName, className: ClassName, namespace: ClassNamespace, attributes?: array<string, CommandAttribute>}
     * @throws \HellYeah\Spawn\Exception\AbstractException
     * @throws \TYPO3\CMS\Core\Package\Exception\UnknownPackageException
     */
    protected function gatherCommonInputs(InputInterface $input, string $type, string $classArgumentName, string $askPrompt, string $defaultName): array
    {
        $extension = $this->askForExtensionName($input);

        $className = $this->askForClassname($input, $type, $classArgumentName, $askPrompt, $defaultName);

        $prefix = $this->askForNamespacePrefix($input, $extension);

        $config = $this->generatorRegistry->getConfig($type);
        $namespace = new ClassNamespace($prefix, $config['subNamespace'], $this->inputValidatorService);

        $inputs = [
            'type' => $type,
            'extension' => $extension,
            'className' => $className,
            'namespace' => $namespace,
        ];

        // Handle additional attributes from registry
        return $this->handleAdditionalInputsFromRegistry($input, $extension, $className, $config, $inputs);
    }

    /**
     * Asks for the extension name interactively or from input.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @return \HellYeah\Spawn\ValueObject\ExtensionName
     * @throws \HellYeah\Spawn\Exception\AbstractException
     */
    protected function askForExtensionName(InputInterface $input): ExtensionName
    {
        if ($extension = $input->getArgument('extension')) {
            $this->inputValidatorService->validateExtensionName($extension);
        }

        $extensions = array_map(fn($ext) => $ext->getValue(), $this->extensionService->getAvailableExtensions());
        $extension = $this->io->choice('Select an extension to work on', $extensions);

        return new ExtensionName($extension, $this->inputValidatorService);
    }

    /**
     * @throws \HellYeah\Spawn\Exception\AbstractException
     */
    protected function askForClassname(InputInterface $input, string $type, string $classArgumentName, string $askPrompt, string $defaultName): ClassName
    {
        $classNameStr = $input->getArgument($classArgumentName);
        if (! $classNameStr) {
            $classNameStr = $this->io->ask(
                $askPrompt,
                $defaultName,
                fn(string $name) => $this->inputValidatorService->validateClassName($name, $type)
            );
        } else {
            $classNameStr = $this->inputValidatorService->validateClassName($classNameStr, $type);
        }

        return new ClassName($classNameStr, $type, $this->inputValidatorService);
    }

    /**
     * @throws \TYPO3\CMS\Core\Package\Exception\UnknownPackageException
     * @throws \HellYeah\Spawn\Exception\AbstractException
     */
    protected function askForNamespacePrefix(InputInterface $input, ExtensionName $extension): NamespacePrefix
    {
        $namespacePrefix = $input->getArgument('namespace');
        if (! $namespacePrefix) {
            $namespacePrefix = $this->extensionService->getPSR4NamespacePrefix($extension)->getValue();
            if (! $namespacePrefix) {
                $namespacePrefix = $this->io->ask(
                    'Enter the namespace prefix (e.g., "Vendor\\Extension\\")',
                    null,
                    fn(string $prefix) => $this->inputValidatorService->validateNamespacePrefix($prefix)
                );
            }
        } else {
            $namespacePrefix = $this->inputValidatorService->validateNamespacePrefix($namespacePrefix);
        }

        return new NamespacePrefix($namespacePrefix, $this->inputValidatorService);
    }

    /**
     * Handles additional attributes and options from the registry.
     *
     * @param InputInterface $input
     * @param ExtensionName $extension
     * @param ClassName $className
     * @param array $config
     * @param array $inputs
     *
     * @return array
     * @throws \HellYeah\Spawn\Exception\AbstractException
     */
    protected function handleAdditionalInputsFromRegistry(
        InputInterface $input,
        ExtensionName $extension,
        ClassName $className,
        array $config,
        array $inputs,
    ): array {
        // Handle attributes (e.g., commandName, commandDescription)
        if (isset($config['attributes']) && is_array($config['attributes'])) {
            $inputs['attributes'] = [];
            foreach ($config['attributes'] as $attribute) {
                $argumentName = $attribute === 'commandName' ? 'command-name' : 'command-description';
                $value = $input->getArgument($argumentName);
                if (! $value) {
                    if ($attribute === 'commandName') {
                        $defaultValue = strtolower($extension->getValue()) . ':' . strtolower(str_replace('Command', '', $className->getValue()));
                        $prompt = 'Enter the command name (e.g., "myext:awesome")';
                    } else {
                        $defaultValue = 'Executes ' . strtolower(str_replace('Command', '', $className->getValue())) . ' action';
                        $prompt = 'Enter the command description (e.g., "Executes awesome action")';
                    }
                    $value = $this->io->ask(
                        $prompt,
                        $defaultValue,
                        fn(string $val) => $attribute === 'commandName'
                            ? $this->inputValidatorService->validateCommandNameAttribute($val)
                            : $this->inputValidatorService->validateCommandDescriptionAttribute($val)
                    );
                } else {
                    $value = $attribute === 'commandName'
                        ? $this->inputValidatorService->validateCommandNameAttribute($value)
                        : $this->inputValidatorService->validateCommandDescriptionAttribute($value);
                }
                $inputs['attributes'][$attribute] = new CommandAttribute($value, $attribute, $this->inputValidatorService);
            }
        }

        // Handle additional options (e.g., schedulable)
        if (isset($config['additionalOptions']) && is_array($config['additionalOptions'])) {
            $inputs['options'] = [];
            foreach ($config['additionalOptions'] as $optionName => $optionConfig) {
                $value = $input->getArgument($optionConfig['argumentName']);
                if (! $value) {
                    if ($optionConfig['type'] === 'boolean' && isset($optionConfig['choices'])) {
                        // Use choice for boolean options
                        $value = $this->io->choice(
                            $optionConfig['prompt'],
                            array_combine($optionConfig['choices'], $optionConfig['choices']),
                            $optionConfig['default']
                        );
                    } else {
                        // Use ask for other types
                        $value = $this->io->ask(
                            $optionConfig['prompt'],
                            $optionConfig['default'],
                            fn(string $val) => $this->inputValidatorService->{$optionConfig['validator']}($val)
                        );
                    }
                } else {
                    $value = $this->inputValidatorService->{$optionConfig['validator']}($value);
                }
                $inputs['options'][$optionName] = $value; // Store as raw value (boolean for schedulable)
            }
        }

        return $inputs;
    }
}
