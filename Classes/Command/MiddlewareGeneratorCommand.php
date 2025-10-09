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

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

#[AsCommand(name:'spawn:middleware', description:'Creates a new TYPO3 middleware')]
class MiddlewareGeneratorCommand extends AbstractGeneratorCommand
{
    protected function configure(): void
    {
        $this
            ->addArgument('extension', InputArgument::OPTIONAL, 'The target extension key (e.g., "my_extension")')
            ->addArgument('class', InputArgument::OPTIONAL, 'The middleware class name (e.g., "AwesomeMiddleware")')
            ->addArgument('namespace', InputArgument::OPTIONAL, 'The namespace prefix (e.g., "Vendor\\Extension\\")');
    }

    /**
     * Gathers and validates inputs for middleware generation.
     *
     * @throws \HellYeah\Spawn\Exception\AbstractException
     * @throws \TYPO3\CMS\Core\Package\Exception\UnknownPackageException
     */
    protected function gatherInputs(InputInterface $input): array
    {
        return $this->gatherCommonInputs(
            $input,
            'middleware',
            'class',
            'Enter the name of the middleware (e.g., "AwesomeMiddleware")',
            'AwesomeMiddleware'
        );
    }
}
