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

class FormatterService
{
    public function addEmptyLineBetweenYamlServices(string $yaml): string
    {
        $lines = explode("\n", $yaml);
        $result = [];

        foreach ($lines as $line) {
            // Match lines that start with two spaces, are not _defaults:, and end with a colon
            if (preg_match('/^ {2}(?!_defaults:)[^ ].*:$/', $line)) {
                // Add a blank line before the service definition if the last line is not already blank
                if (count($result) > 0 && trim(end($result)) !== '') {
                    $result[] = '';
                }
            }
            $result[] = $line;
        }

        return implode("\n", $result);
    }
}
