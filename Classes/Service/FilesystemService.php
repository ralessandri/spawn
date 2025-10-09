<?php

declare(strict_types=1);

namespace HellYeah\Spawn\Service;

use HellYeah\Spawn\Exception\InvalidArgumentException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Service for filesystem operations.
 */
class FilesystemService
{
    /**
     * Checks if a file exists and is allowed.
     *
     * @throws InvalidArgumentException If path is invalid.
     */
    public function fileExists(string $path): bool
    {
        if (! GeneralUtility::validPathStr($path) || ! GeneralUtility::isAllowedAbsPath($path)) {
            throw new InvalidArgumentException(
                sprintf('Invalid file path: %s', $path),
                1759257206
            );
        }

        return file_exists($path);
    }

    /**
     * Reads file content.
     *
     * @throws InvalidArgumentException If file not found or unreadable.
     */
    public function readFile(string $path): string
    {
        if (! $this->fileExists($path)) {
            throw new InvalidArgumentException(
                sprintf('File not found: %s', $path),
                1759257207
            );
        }

        $content = GeneralUtility::getUrl($path);
        if ($content === false) {
            throw new InvalidArgumentException(
                sprintf('Could not read file: %s', $path),
                1759257208
            );
        }

        return $content;
    }
}
