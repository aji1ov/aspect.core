<?php

use Aspect\Lib\Application;
use Aspect\Lib\Facade\Command;
use Aspect\Lib\Preset\Command\Orm\Table\Create;
use Bitrix\Main\EventManager;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

class Aspect_Core extends CModule
{
    private const MODULE_LANG_PREFIX = 'ASPECT_CORE__MODULE_';

    public function __construct()
    {
        $arModuleVersion = [];
        include(__DIR__ . "/version.php");

        $this->MODULE_ID = 'aspect.core';
        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = Loc::getMessage(static::MODULE_LANG_PREFIX."NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage(static::MODULE_LANG_PREFIX."DESC");

        $this->PARTNER_NAME = Loc::getMessage(static::MODULE_LANG_PREFIX."PARTNER_NAME");
        $this->PARTNER_URI = Loc::getMessage(static::MODULE_LANG_PREFIX."PARTNER_URI");

    }

    /**
     * @throws Exception
     */
    public function doInstall(): ?bool
    {
        if(!class_exists(Application::class)) {
            global $APPLICATION;
            $APPLICATION->ThrowException(Loc::getMessage('ASPECT_CORE__MODULE_COMPOSER_REQUIRED'));
            return false;
        }

        EventManager::getInstance()->registerEventHandler('main', 'OnProlog', 'aspect.core');

        $this->unzip(__DIR__.'/../wizard/php_interface.zip', __DIR__.'/../../../php_interface/');
        $this->unzip(__DIR__.'/../wizard/components.zip', __DIR__.'/../../../../bitrix/components');
        $this->unzip(__DIR__.'/../wizard/extensions.zip', __DIR__.'/../../../../local/js/');

        touch(__DIR__.'/../../../../aspect');
        copy(__DIR__.'/../wizard/aspect', __DIR__.'/../../../../aspect');

        $this->createOrm(\Aspect\Lib\Table\JobLogTable::class);
        $this->createOrm(\Aspect\Lib\Table\QueueTable::class);
        $this->createOrm(\Aspect\Lib\Table\ScheduleTable::class);
        $this->createOrm(\Aspect\Lib\Table\ScriptTable::class);

        ModuleManager::registerModule($this->MODULE_ID);

        return true;
    }

    /**
     * @throws \Bitrix\Main\SystemException
     * @throws \Bitrix\Main\ArgumentException
     */
    private function createOrm(string $tableClass): void
    {
        $connection = \Bitrix\Main\Application::getConnection();

        assert(is_a($tableClass, \Bitrix\Main\Entity\DataManager::class, true));
        if(!$connection->isTableExists($tableClass::getTableName())) {
            $tableClass::getEntity()->createDbTable();
        }
    }

    private function unzip(string $pathToArchive, string $pathToExtract): void
    {
        $zip = new \ZipArchive();
        if ($zip->open($pathToArchive) === TRUE) {
            $zip->extractTo($pathToExtract);
            $zip->close();
        }
    }

    public function doUninstall(): void
    {
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }
}