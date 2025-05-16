<?php

namespace Aspect\Lib\Facade;

use Bitrix\Main\Loader;

final class Yakov
{
    private const MODULE_ID = 'aspect.core';

    private const PATH_MAP = [
        'core' => [
            'namespace' => 'Aspect\\Lib\\Preset\\#PATH#\\',
            'folder' => '#MODULE_DIR#/src/Preset/#PATH#/'
        ],
        'app' => [
            'namespace' => 'Aspect\\App\\#PATH#\\',
            'folder' => '#APP_DIR#/#PATH#/'
        ]
    ];

    private static function getPath(string $path, string $data): string
    {
        return str_replace(
            ['#PATH#', '#MODULE_DIR#', '#APP_DIR#'],
            [$path, self::getModuleDir(), self::getNamespaceDir()],
            $data
        );
    }

    private static function compileExtension(string $path, string $namespace, string $folder): array
    {
        return [
            self::getPath($path, $namespace) => self::getPath($path, $folder)
        ];
    }

    public static function getExtensions(string $path, bool $withCore = true): array
    {
        if ($withCore) {
            $extensions[] = self::compileExtension(
                $path,
                self::PATH_MAP['core']['namespace'],
                self::PATH_MAP['core']['folder']
            );
        }

        $extensions[] = self::compileExtension(
            $path,
            self::PATH_MAP['app']['namespace'],
            self::PATH_MAP['app']['folder']
        );

        return array_merge(...$extensions);

    }

    public static function getExtensionConfigFile(string $file): string
    {
        return self::getNamespaceDir($file);
    }

    public static function getModuleDir(): bool|string
    {
        return Loader::getPersonal('modules/' . self::MODULE_ID);
    }

    private static function getNamespaceDir(?string $tail = null): bool|string
    {
        return Loader::getPersonal('php_interface/lib' . ($tail ? '/' . $tail : ''));
    }

    public static function getPathToCommands(): array
    {
        return self::getExtensions('Command');
    }

    public static function getPathToRuntime(): array
    {
        return self::getExtensions('Runtime');
    }

    public static function getPathToEvents(): array
    {
        return self::getExtensions('Event');
    }

    public static function getPathToFactories(): array
    {
        return self::getExtensions('Factory');
    }

    public static function getCmdBundlesPath(): string
    {
        return self::getNamespaceDir('cmd/');
    }

    public static function getScheduleEventsPath(): string
    {
        return self::getExtensionConfigFile('schedule.php');
    }

    public static function getFactoryPath(): string
    {
        return self::getExtensionConfigFile('factory.php');
    }
}