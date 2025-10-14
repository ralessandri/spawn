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
 * Value Object for the schedulable attribute of a command.
 */
class Schedulable
{
    private string $value;

    /**
     * @throws \HellYeah\Spawn\Exception\InvalidArgumentException
     */
    public function __construct(
        string $value,
        string $type,
        private readonly InputValidatorService $validatorService,
    ) {
        $this->value = $this->validatorService->validateBoolean($value) ? 'true' : 'false';
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
