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
use HellYeah\Spawn\ValueObject\ExtensionName;
use HellYeah\Spawn\ValueObject\NamespacePrefix;
use TYPO3\CMS\Core\Package\PackageManager;

/**
 * Service for TYPO3 extension operations.
 */
readonly class ExtensionService
{
    public function __construct(
        private PackageManager $packageManager,
        private FilesystemService $filesystemService,
        private InputValidatorService $inputValidatorService,
    ) {}

    /**
     * Retrieves available non-core extensions.
     *
     * @return ExtensionName[] List of extensions.
     * @throws \HellYeah\Spawn\Exception\AbstractException
     */
    public function getAvailableExtensions(): array
    {
        $result = [];
        $loadedExtensions = $this->packageManager->getActivePackages();

        foreach ($loadedExtensions as $extension) {
            $extensionKey = $extension->getPackageKey();
            $path = $extension->getPackagePath();
            if (! str_contains($path, '/typo3/cms')) {
                $result[$extensionKey] = new ExtensionName($extensionKey, $this->inputValidatorService);
            }
        }

        return $result;
    }

    /**
     * Retrieves PSR-4 namespace prefix, ensuring trailing backslash.
     *
     * @throws \HellYeah\Spawn\Exception\AbstractException
     * @throws \TYPO3\CMS\Core\Package\Exception\UnknownPackageException
     */
    public function getPSR4NamespacePrefix(ExtensionName $extension): NamespacePrefix
    {
        $composerJsonPath = $this->getExtensionPath($extension) . 'composer.json';

        if (! $this->filesystemService->fileExists($composerJsonPath)) {
            throw new InvalidArgumentException(
                sprintf('No composer.json found for extension "%s"', $extension->getValue()),
                1759257202
            );
        }

        $composerJson = $this->readComposerJson($composerJsonPath);
        $psr4 = $composerJson['autoload']['psr-4'] ?? null;

        if (! is_array($psr4) || empty($psr4)) {
            throw new InvalidArgumentException(
                sprintf('No PSR-4 autoload defined in composer.json for extension "%s"', $extension->getValue()),
                1759257203
            );
        }

        $namespace = array_key_first($psr4);
        if (! str_ends_with($namespace, '\\')) {
            $namespace .= '\\';
        }

        return new NamespacePrefix($namespace, $this->inputValidatorService);
    }

    /**
     * Returns the extension directory path.
     *
     * @throws \TYPO3\CMS\Core\Package\Exception\UnknownPackageException
     */
    public function getExtensionPath(ExtensionName $extension): string
    {
        $package = $this->packageManager->getPackage($extension->getValue());

        return $package->getPackagePath();
    }

    /**
     * Reads and decodes composer.json.
     *
     * @throws \HellYeah\Spawn\Exception\AbstractException
     */
    private function readComposerJson(string $path): array
    {
        $content = $this->filesystemService->readFile($path);
        $decoded = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException(
                sprintf('Invalid JSON in composer.json: %s', json_last_error_msg()),
                1759257204
            );
        }

        if (! is_array($decoded)) {
            throw new InvalidArgumentException('composer.json does not contain valid data', 1759257205);
        }

        return $decoded;
    }
}
