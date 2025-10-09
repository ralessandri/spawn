<?php

declare(strict_types=1);

namespace HellYeah\Spawn\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Event dispatched before generation.
 */
class PreGenerateEvent extends Event
{
    public const string NAME = 'spawn.pre_generate';

    public function __construct(public array $inputs) {}
}
