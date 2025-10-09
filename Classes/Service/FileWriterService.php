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
    public function write(string $path, string $content): void
    {
        $dirname = dirname($path);

        if (! GeneralUtility::validPathStr($path) || ! GeneralUtility::isAllowedAbsPath($path)) {
            throw new InvalidArgumentException(
                'Invalid path: ' . $path,
                1759257199
            );
        }

        if (file_exists($path)) {
            throw new FileExistsException(
                'File already exists: ' . $path,
                1759257194
            );
        }

        if (! file_exists($dirname)) {
            GeneralUtility::mkdir_deep($dirname);
        }

        GeneralUtility::writeFile($path, $content);
    }
}
