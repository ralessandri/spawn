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

use HellYeah\Spawn\Exception\InvalidConfigurationException;
use TYPO3\CMS\Core\Package\PackageManager;

/**
 * Service to provide generator configuration.
 */
class GeneratorRegistryService
{
    private array $config = [];

    /**
     * @throws \TYPO3\CMS\Core\Package\Exception\UnknownPackageException
     * @throws \HellYeah\Spawn\Exception\AbstractException
     */
    public function __construct(
        private readonly FilesystemService $filesystemService,
        private readonly PackageManager $packageManager,
    ) {
        $this->loadConfiguration();
    }

    /**
     * Loads the generator configuration from Configuration/Spawn.php.
     *
     * @throws \HellYeah\Spawn\Exception\AbstractException
     * @throws \TYPO3\CMS\Core\Package\Exception\UnknownPackageException
     */
    private function loadConfiguration(): void
    {
        $package = $this->packageManager->getPackage('spawn');
        $configFile = $package->getPackagePath() . 'Configuration/Spawn.php';

        if (! $this->filesystemService->fileExists($configFile)) {
            throw new InvalidConfigurationException(
                sprintf('Configuration file "%s" not found', $configFile),
                1738923456
            );
        }

        $config = include $configFile;

        if (! is_array($config)) {
            throw new InvalidConfigurationException(
                sprintf('Configuration file "%s" must return an array', $configFile),
                1760453783768
            );
        }

        $this->config = $config;
    }

    /**
     * Returns the configuration for a given generator type.
     *
     * @throws InvalidConfigurationException
     */
    public function getConfig(string $type): array
    {
        if (! isset($this->config[$type])) {
            throw new InvalidConfigurationException(
                sprintf('No configuration found for type "%s"', $type),
                1760453783781
            );
        }

        return $this->config[$type];
    }
}
