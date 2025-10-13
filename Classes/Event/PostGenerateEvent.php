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

namespace HellYeah\Spawn\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Event dispatched after generation.
 */
class PostGenerateEvent extends Event
{
    public const string NAME = 'spawn.post_generate';

    public function __construct(public array $inputs, public string $targetPath) {}

    public function getInputs(): array
    {
        return $this->inputs;
    }

    public function getTargetPath(): string
    {
        return $this->targetPath;
    }
}
