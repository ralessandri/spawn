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
 * Immutable value object representing a Symfony command attribute (name or description).
 */
class CommandAttribute
{
    private string $value;
    private string $type;

    /**
     * @param string $value The attribute value.
     * @param string $type 'name' or 'description'.
     * @param InputValidatorService $validator Injected validator for delegation.
     *
     * @throws \HellYeah\Spawn\Exception\AbstractException
     */
    public function __construct(string $value, string $type, InputValidatorService $validator)
    {
        $this->type = $type;
        if ($type === 'name') {
            $this->value = $validator->validateCommandNameAttribute($value);
        } else {
            $this->value = $validator->validateCommandDescriptionAttribute($value);
        }
    }

    /**
     * Returns the attribute value.
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Returns the attribute type.
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Returns the string representation.
     */
    public function __toString(): string
    {
        return $this->value;
    }
}
