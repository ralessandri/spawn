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

namespace HellYeah\Spawn\Service;

use HellYeah\Spawn\Exception\InvalidArgumentException;

/**
 * Registry for class generation configurations, allowing scalable addition of types.
 */
class GeneratorRegistryService
{
    private array $configuration = [];

    public function __construct()
    {
        $this->loadConfiguration();
    }

    /**
     * Loads generation configurations from a YAML file or array.
     */
    private function loadConfiguration(): void
    {
        // Example: Load from YAML or hardcode; in production, use YAML for extensibility
        $this->configuration = [
            'controller' => [
                'templatePath' => 'EXT:spawn/Resources/Private/Php/Templates/Classes/Controller/TemplateController.php',
                'subNamespace' => 'Controller',
                'targetDir' => 'Classes/Controller/',
                'classNameSuffix' => 'Controller',
            ],
            'command' => [
                'templatePath' => 'EXT:spawn/Resources/Private/Php/Templates/Classes/Command/TemplateCommand.php',
                'subNamespace' => 'Command',
                'targetDir' => 'Classes/Command/',
                'classNameSuffix' => 'Command',
                'attributes' => ['commandName', 'commandDescription'],
                'postHooks' => ['addCommandToServicesYaml'],
                'additionalOptions' => [
                    'schedulable' => [
                        'type' => 'boolean',
                        'argumentName' => 'schedulable',
                        'prompt' => 'Should the command be schedulable? (false/true)',
                        'choices' => ['false', 'true'],
                        'default' => 'false',
                        'validator' => 'validateBoolean',
                    ],
                ],
            ],
            'middleware' => [
                'templatePath' => 'EXT:spawn/Resources/Private/Php/Templates/Classes/Middleware/TemplateMiddleware.php',
                'subNamespace' => 'Middleware',
                'targetDir' => 'Classes/Middleware/',
                'classNameSuffix' => 'Middleware',
            ],
            'model' => [
                'templatePath' => 'EXT:spawn/Resources/Private/Php/Templates/Classes/Model/TemplateModel.php',
                'subNamespace' => 'Domain\Model',
                'targetDir' => 'Classes/Domain/Model/',
                'classNameSuffix' => '',
            ],
            'repository' => [
                'templatePath' => 'EXT:spawn/Resources/Private/Php/Templates/Classes/Repository/TemplateRepository.php',
                'subNamespace' => 'Domain\Repository',
                'targetDir' => 'Classes/Domain/Repository/',
                'classNameSuffix' => 'Repository',
            ],
            'event' => [
                'templatePath' => 'EXT:spawn/Resources/Private/Php/Templates/Classes/Event/TemplateEvent.php',
                'subNamespace' => 'Event',
                'targetDir' => 'Classes/Event/',
                'classNameSuffix' => 'Event',
            ],
            // Add more types as needed, e.g. 'service', 'viewhelper'
        ];
    }

    /**
     * Retrieves the configuration for a specific generation type.
     *
     * @throws InvalidArgumentException If the type is not registered.
     */
    public function getConfig(string $type): array
    {
        if (! isset($this->configuration[$type])) {
            throw new InvalidArgumentException(
                sprintf('Unknown generation type: "%s"', $type),
                1759257210
            );
        }

        return $this->configuration[$type];
    }

    /**
     * Returns a list of registered types for choice prompts.
     */
    public function getRegisteredTypes(): array
    {
        return array_keys($this->configuration);
    }
}
