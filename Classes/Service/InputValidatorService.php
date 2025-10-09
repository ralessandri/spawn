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
use HellYeah\Spawn\Exception\InvalidAttributeDescriptionException;
use HellYeah\Spawn\Exception\InvalidCommandNameException;
use HellYeah\Spawn\Exception\InvalidControllerNameException;
use HellYeah\Spawn\Exception\InvalidMiddlewareNameException;
use HellYeah\Spawn\Exception\InvalidNamespacePrefixException;

/**
 * Centralized service for all input validations in code generation.
 */
class InputValidatorService
{
    /**
     * Validates an extension name.
     *
     * @throws InvalidArgumentException If invalid.
     */
    public function validateExtensionName(string $extensionName): string
    {
        if (preg_match('/^[a-z][a-z0-9_]*$/', $extensionName) !== 1) {
            throw new InvalidArgumentException(
                'Invalid extension name: Must be lowercase, alphanumeric with underscores, e.g., "my_extension"',
                1759257200
            );
        }

        return $extensionName;
    }

    /**
     * Validates a class name based on type.
     *
     * @throws \HellYeah\Spawn\Exception\AbstractException
     */
    public function validateClassName(string $className, string $type): string
    {
        if (preg_match('/^[A-Z][a-zA-Z0-9]*$/', $className) !== 1) {
            throw new InvalidArgumentException(
                'Invalid class name: Must start with a capital letter and contain only alphanumeric characters',
                1759257201
            );
        }

        return match ($type) {
            'controller' => $this->validateControllerClassName($className),
            'command' => $this->validateCommandClassName($className),
            'middleware' => $this->validateMiddlewareClassName($className),
            'model' => $className, // No suffix for models
            'repository' => $this->validateRepositoryClassName($className), // Assume added method
            'event' => $this->validateEventClassName($className), // Assume added method
            default => throw new InvalidArgumentException(
                "Unsupported class type: $type",
                1759257211
            ),
        };
    }

    /**
     * @throws \HellYeah\Spawn\Exception\AbstractException
     */
    private function validateControllerClassName(string $className): string
    {
        if (! str_ends_with($className, 'Controller')) {
            throw new InvalidControllerNameException(
                'Invalid controller name: Must end with "Controller"',
                1759153533
            );
        }

        return $className;
    }

    /**
     * @param string $className
     *
     * @return string
     * @throws \HellYeah\Spawn\Exception\AbstractException
     */
    private function validateCommandClassName(string $className): string
    {
        if (! str_ends_with($className, 'Command')) {
            throw new InvalidCommandNameException(
                'Invalid command name: Must end with "Command"',
                1759675263
            );
        }

        return $className;
    }

    /**
     * @param string $className
     *
     * @return string
     * @throws \HellYeah\Spawn\Exception\AbstractException
     */
    private function validateMiddlewareClassName(string $className): string
    {
        if (! str_ends_with($className, 'Middleware')) {
            throw new InvalidMiddlewareNameException(
                'Invalid middleware name: Must end with "Middleware"',
                1759935501
            );
        }

        return $className;
    }

    /**
     * @param string $className
     *
     * @return string
     * @throws \HellYeah\Spawn\Exception\AbstractException
     */
    private function validateRepositoryClassName(string $className): string
    {
        if (! str_ends_with($className, 'Repository')) {
            throw new InvalidArgumentException(
                'Invalid repository name: Must end with "Repository"',
                1759257212
            );
        }

        return $className;
    }

    /**
     * @param string $className
     *
     * @return string
     * @throws \HellYeah\Spawn\Exception\AbstractException
     */
    private function validateEventClassName(string $className): string
    {
        if (! str_ends_with($className, 'Event')) {
            throw new InvalidArgumentException(
                'Invalid event name: Must end with "Event"',
                1759257213
            );
        }

        return $className;
    }

    /**
     * Validates a namespace prefix.
     *
     * @throws InvalidNamespacePrefixException If invalid.
     */
    public function validateNamespacePrefix(string $namespacePrefix): string
    {
        if (preg_match('/^[A-Z][a-zA-Z0-9]*(\\\\[A-Z][a-zA-Z0-9]*)*\\\\$/', $namespacePrefix) !== 1) {
            throw new InvalidNamespacePrefixException(
                'Invalid namespace prefix: Must follow PSR-4 with trailing backslash, e.g., "Vendor\\Extension\\"',
                1759256166
            );
        }

        return $namespacePrefix;
    }

    /**
     * Validates a sub-namespace based on type.
     *
     * @throws InvalidArgumentException If invalid.
     */
    public function validateSubNamespace(string $subNamespace): string
    {
        if (preg_match('/^[A-Z][a-zA-Z0-9]*(\\\\[A-Z][a-zA-Z0-9]*)*$/', $subNamespace) !== 1) {
            throw new InvalidArgumentException(
                'Invalid sub-namespace: Must be PascalCase path, e.g., "Controller" or "Domain\\Model"',
                1759257209
            );
        }

        return $subNamespace;
    }

    /**
     * Validates a command name attribute.
     *
     * @throws InvalidCommandNameException If invalid.
     */
    public function validateCommandNameAttribute(string $commandName): string
    {
        if (preg_match('/^[a-z][a-z0-9-]*:[a-z][a-z0-9-]*$/', $commandName) !== 1) {
            throw new InvalidCommandNameException(
                'Invalid command name: Must be in format "namespace:command"',
                1759676011
            );
        }

        return $commandName;
    }

    /**
     * Validates a command description attribute.
     *
     * @throws InvalidAttributeDescriptionException If invalid.
     */
    public function validateCommandDescriptionAttribute(string $description): string
    {
        $trimmed = trim($description);
        if (empty($trimmed)) {
            throw new InvalidAttributeDescriptionException(
                'Invalid command description: Must be a non-empty string',
                1759676689
            );
        }

        return $trimmed;
    }

    /**
     * Validates the generation type.
     *
     * @throws InvalidArgumentException If invalid.
     */
    public function validateType(string $type): string
    {
        $allowedTypes = ['controller', 'command', 'middleware', 'model', 'repository', 'event'];
        if (! in_array($type, $allowedTypes, true)) {
            throw new InvalidArgumentException(
                sprintf('Invalid generation type: "%s". Allowed: %s', $type, implode(', ', $allowedTypes)),
                1759257214
            );
        }

        return $type;
    }
}
