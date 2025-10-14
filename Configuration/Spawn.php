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

use HellYeah\Spawn\ValueObject\CommandAttribute;
use HellYeah\Spawn\ValueObject\Schedulable;

return [
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
            'commandName' => [
                'type' => 'string',
                'argumentName' => 'command-name',
                'prompt' => 'Enter the command name (e.g., "myext:awesome")',
                'default' => null,
                'validator' => 'validateCommandNameAttribute',
                'valueObject' => CommandAttribute::class,
            ],
            'commandDescription' => [
                'type' => 'string',
                'argumentName' => 'command-description',
                'prompt' => 'Enter the command description (e.g., "Executes awesome action")',
                'default' => null,
                'validator' => 'validateCommandDescriptionAttribute',
                'valueObject' => CommandAttribute::class,
            ],
            'schedulable' => [
                'type' => 'boolean',
                'argumentName' => 'schedulable',
                'prompt' => 'Should the command be schedulable? (no/yes)',
                'choices' => ['no', 'yes'],
                'default' => 'no',
                'validator' => 'validateBoolean',
                'valueObject' => Schedulable::class,
            ],
        ],
    ],
    'middleware' => [
        'templatePath' => 'EXT:spawn/Resources/Private/Php/Templates/Classes/Middleware/TemplateMiddleware.php',
        'subNamespace' => 'Middleware',
        'targetDir' => 'Classes/Middleware/',
        'classNameSuffix' => 'Middleware',
    ],
];
