<?php

namespace Aspect\Lib\Service\Admin;

use Aspect\Lib\Blueprint\DI\Fetch;
use Aspect\Lib\Cache\HealthCache;
use Aspect\Lib\Struct\ServiceLocator;
use Bitrix\Main\Config\Option;
use CAdminNotify;

class AdminNotifyService
{
    use ServiceLocator;

    public function markJobHealthy(): void
    {
        Option::set('aspect.core', 'last_queue_exec', time());
    }

    public function isJobExecutorInstalled(): bool
    {
        return Option::get('aspect.core', 'last_queue_exec') > 0;
    }

    public function isJobExecutorHealthy(): bool
    {
        return $this->getLastJobExecutorRunning() + 3600 > time();
    }

    public function getLastJobExecutorRunning(): int
    {
        return (int) Option::get('aspect.core', 'last_queue_exec', 0);
    }

    public function checkJobQueue(): void
    {
        if ($this->isJobExecutorHealthy()) {
           $this->deleteNotifications();
           return;
        }

        if (!$this->isJobExecutorInstalled()) {
            $this->sendQueueExecutorNotInstalledNotification();
           return;
        }

        if (!$this->isJobExecutorHealthy()) {
            $this->sendQueueExecutorIsLateNotification();
        }
    }

    private function sendQueueExecutorNotInstalledNotification(): void
    {
        CAdminNotify::Add([
            'MESSAGE' => 'Для корректной работы модуля <em>aspect.core</em> необходимо <a href="/bitrix/admin/aspect_info_cron.php"><b>завершить настройку</b></a>.',
            'TAG' => 'aspect_core_queue_executor',
            'MODULE_ID' => 'aspect.core',
            'ENABLE_CLOSE' => 'Y',
        ]);
    }

    private function sendQueueExecutorIsLateNotification(): void
    {
        CAdminNotify::Add([
            'MESSAGE' => 'Планировщик задач <a href="/bitrix/admin/aspect_info_cron.php"><b>работает некорректно</b></a>.',
            'TAG' => 'aspect_core_queue_executor',
            'MODULE_ID' => 'aspect.core',
            'ENABLE_CLOSE' => 'Y',
        ]);
    }

    private function deleteNotifications(): void
    {
        CAdminNotify::DeleteByTag('aspect_core_queue_executor');
    }
}