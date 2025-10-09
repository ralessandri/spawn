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

namespace HellYeah\Spawn\ValueObject;

use HellYeah\Spawn\Service\InputValidatorService;

/**
 * Immutable value object representing a PHP class name.
 */
class ClassName
{
    private string $value;

    /**
     * @param string $value The class name.
     * @param string $type The class type (controller, command...))
     * @param InputValidatorService $validator Injected validator for delegation.
     *
     * @throws \HellYeah\Spawn\Exception\AbstractException
     */
    public function __construct(string $value, string $type, InputValidatorService $validator)
    {
        $this->value = $validator->validateClassName($value, $type); // Assume general validateClassName in service
    }

    /**
     * Returns the class name.
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Returns the string representation.
     */
    public function __toString(): string
    {
        return $this->value;
    }
}
