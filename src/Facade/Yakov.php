<?php

namespace Aspect\Lib\Facade;

use Bitrix\Main\Loader;

final class Yakov
{
    private const MODULE_ID = 'aspect.core';

    public static function getModuleDir(): bool|string
    {
        return Loader::getPersonal('modules/'.self::MODULE_ID);
    }

    public static function getNamespaceDir(?string $tail = null): bool|string
    {
        return Loader::getPersonal('php_interface/lib'.($tail ? '/'.$tail : ''));
    }

    public static function getFactoryPath(): bool|string
    {
        return Yakov::getNamespaceDir('factory.php');
    }

    public static function getPathToCommands(): array
    {
        return [
            'Aspect\\Lib\\Preset\\Command\\' => Yakov::getModuleDir().'/src/Preset/Command/',
            'Aspect\\App\\Command\\' => Yakov::getNamespaceDir('Command/')
        ];
    }

    public static function getPathToEvents(): array
    {
        return [
            'Aspect\\Lib\\Preset\\Event\\' => Yakov::getModuleDir().'/src/Preset/Event/',
            'Aspect\\App\\Event\\' => Yakov::getNamespaceDir('Event/')
        ];
    }

    public static function getPathToFactories(): array
    {
        return [
            'Aspect\\Lib\\Preset\\Factory\\' => Yakov::getModuleDir().'/src/Preset/Factory/',
            'Aspect\\App\\Factory\\' => Yakov::getNamespaceDir('Factory/')
        ];
    }

    public static function getCmdBundlesPath(): bool|string
    {
        return Yakov::getNamespaceDir('cmd/');
    }

    public static function getScheduleEventsPath(): bool|string
    {
        return Yakov::getNamespaceDir('schedule.php');
    }
}