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

use TYPO3\CMS\Core\Utility\GeneralUtility;

readonly class TemplateLoaderService
{
    public function __construct(private FilesystemService $filesystemService) {}

    /**
     * Loads a template file content.
     *
     * @throws \HellYeah\Spawn\Exception\InvalidArgumentException
     */
    public function load(string $templatePath): string
    {
        $absolutePath = GeneralUtility::getFileAbsFileName($templatePath);

        return $this->filesystemService->readFile($absolutePath);
    }

}
