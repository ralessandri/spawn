<?php

declare(strict_types=1);

namespace HellYeah\Spawn\Service;

use HellYeah\Spawn\Exception\FileExistsException;
use HellYeah\Spawn\Exception\InvalidArgumentException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Service for writing files securely.
 */
class FileWriterService
{
    /**
     * Writes content to a file, checking for existence and validity.
     *
     * @throws InvalidArgumentException|FileExistsException If invalid or exists.
     */
    public function write(string $path, string $content, bool $override = false): void
    {
        $dirname = dirname($path);

        // Validate path
        if (! GeneralUtility::validPathStr($path) || ! GeneralUtility::isAllowedAbsPath($path)) {
            throw new InvalidArgumentException(
                'Invalid path: ' . $path,
                1759257199
            );
        }

        // Check if file exists and override is not allowed
        if (file_exists($path) && $override === false) {
            throw new FileExistsException(
                'File already exists: ' . $path,
                1759257194
            );
        }

        // Ensure directory exists
        if (! file_exists($dirname)) {
            GeneralUtility::mkdir_deep($dirname);
        }

        // Write content to file
        GeneralUtility::writeFile($path, $content);
    }
}
