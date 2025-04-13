<?php

namespace Aspect\Lib\Preset\Event;

use Aspect\Lib\Blueprint\Event\ArrayEvent;
use Aspect\Lib\Event\EventPackage;
use Aspect\Lib\Event\ListEventSource;
use Aspect\Lib\Preset\Background\HealthJob;
use Aspect\Lib\Service\Admin\AdminMenuService;
use Aspect\Lib\Service\Admin\AdminNotifyService;
use CAdminNotify;

class Main extends EventPackage
{
    #[ArrayEvent(takeTo: ['global', 'module'])]
    public function OnBuildGlobalMenu(ListEventSource $source, AdminNotifyService $adminNotifyService, AdminMenuService $adminMenuService)
    {
        $source->setOutput(
            'global',
            $adminMenuService->enrichGlobalMenu($source->getValue('global'))
        );

        global $APPLICATION;
        if ($APPLICATION->GetCurPage() === '/bitrix/admin/') {
            $adminNotifyService->checkJobQueue();
        }

    }
}