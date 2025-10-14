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

namespace HellYeah\Spawn\EventListener;

use HellYeah\Spawn\Event\PostGenerateEvent;
use HellYeah\Spawn\Service\ExtensionService;
use HellYeah\Spawn\Service\FilesystemService;
use HellYeah\Spawn\Service\FileWriterService;
use HellYeah\Spawn\Service\FormatterService;
use HellYeah\Spawn\Service\GeneratorRegistryService;
use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\Core\Attribute\AsEventListener;

/**
 * Listener to update Services.yaml with command-related tags after generation.
 */
#[AsEventListener(identifier: 'spawn/add-command-to-services-yaml', event: PostGenerateEvent::class)]
final class AddCommandToServicesYaml
{
    private const array SUPPORTED_TYPES = ['command'];

    private const string HOOK_NAME = 'addCommandToServicesYaml';

    public function __construct(
        private readonly GeneratorRegistryService $generatorRegistry,
        private readonly ExtensionService $extensionService,
        private readonly FileWriterService $fileWriterService,
        private readonly FileSystemService $fileSystemService,
        private readonly FormatterService $formatterService,
    ) {}

    /**
     * Handles the PostGenerateEvent to update Services.yaml with command tags.
     *
     * @throws \HellYeah\Spawn\Exception\AbstractException
     * @throws \TYPO3\CMS\Core\Package\Exception\UnknownPackageException
     */
    public function __invoke(PostGenerateEvent $event): void
    {
        $inputs = $event->getInputs();
        $type = $inputs['type'] ?? null;

        if (! $this->shouldProcess($type, $inputs)) {
            return;
        }

        $servicesFile = $this->getServicesFilePath($inputs['extension']);
        $yaml = $this->loadServicesYaml($servicesFile);
        $yaml = $this->updateServiceConfig($yaml, $inputs);
        $this->writeServicesYaml($servicesFile, $yaml);
    }

    /**
     * Checks if the event should be processed based on type, hooks, and attributes.
     *
     * @throws \HellYeah\Spawn\Exception\InvalidArgumentException
     */
    private function shouldProcess(?string $type, array $inputs): bool
    {
        if (! in_array($type, self::SUPPORTED_TYPES, true)) {
            return false;
        }

        $config = $this->generatorRegistry->getConfig($type);
        if (! in_array(self::HOOK_NAME, $config['postHooks'] ?? [], true)) {
            return false;
        }

        // Ensure required attributes exist
        return isset($inputs['attributes']['commandName'], $inputs['attributes']['commandDescription']);
    }

    /**
     * Determines the path to the Services.yaml file.
     *
     * @throws \TYPO3\CMS\Core\Package\Exception\UnknownPackageException
     */
    private function getServicesFilePath(mixed $extension): string
    {
        return $this->extensionService->getExtensionPath($extension) . 'Configuration/Services.yaml';
    }

    /**
     * Loads and parses the Services.yaml file, or returns an empty structure.
     *
     * @throws \HellYeah\Spawn\Exception\InvalidArgumentException
     */
    private function loadServicesYaml(string $servicesFile): array
    {
        $content = $this->fileSystemService->readFile($servicesFile);

        return $content ? Yaml::parse($content) ?? ['services' => []] : ['services' => []];
    }

    /**
     * Updates the service configuration with command tags.
     */
    private function updateServiceConfig(array $yaml, array $inputs): array
    {
        $serviceId = $inputs['namespace'] . '\\' . $inputs['className'];
        $commandTag = [
            'name' => 'console.command',
            'command' => $inputs['attributes']['commandName']->getValue(),
            'description' => $inputs['attributes']['commandDescription']->getValue(),
        ];

        // Add schedulable only if explicitly set to 'false'
        if (isset($inputs['options']['schedulable']) && $inputs['options']['schedulable'] === 'false') {
            $commandTag['schedulable'] = false;
        }

        // Ensure the 'services' key exists
        if (! isset($yaml['services'])) {
            $yaml['services'] = [];
        }

        // Ensure the specific service entry exists
        if (! isset($yaml['services'][$serviceId])) {
            $yaml['services'][$serviceId] = ['tags' => []];
        }

        $serviceConfig = $yaml['services'][$serviceId];
        $tags = $serviceConfig['tags'] ?? [];

        // Update or add the command tag
        $tags = $this->updateCommandTag($tags, $commandTag);

        $serviceConfig['tags'] = $tags;
        $yaml['services'][$serviceId] = $serviceConfig;

        return $yaml;
    }

    /**
     * Updates or adds the command tag, avoiding duplicates.
     */
    private function updateCommandTag(array $existingTags, array $newTag): array
    {
        $updatedTags = [];
        $found = false;

        foreach ($existingTags as $tag) {
            if ($tag['name'] === 'console.command') {
                // Update existing console.command tag
                $updatedTag = array_merge($tag, array_filter($newTag, fn($key) => $key !== 'name', ARRAY_FILTER_USE_KEY));
                $updatedTags[] = $updatedTag;
                $found = true;
            } else {
                $updatedTags[] = $tag;
            }
        }

        // Add new tag if no console.command tag was found
        if (! $found) {
            $updatedTags[] = $newTag;
        }

        return $updatedTags;
    }

    /**
     * Writes the updated Services.yaml file.
     *
     * @throws \HellYeah\Spawn\Exception\AbstractException
     */
    private function writeServicesYaml(string $servicesFile, array $yaml): void
    {
        $content = $this->formatterService->addEmptyLineBetweenYamlServices(Yaml::dump($yaml, 99, 2));
        $this->fileWriterService->write($servicesFile, $content, true);
    }
}
