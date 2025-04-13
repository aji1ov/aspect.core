<?php

namespace Aspect\Lib\Preset\Background;

use Aspect\Lib\Blueprint\DI\Fetch;
use Aspect\Lib\Service\Admin\AdminNotifyService;
use Aspect\Lib\Service\Background\Job;

class HealthJob extends Job
{

    #[Fetch]
    private AdminNotifyService $adminNotifyService;

    public function __construct()
    {
        $this->setUnique(true);
    }

    public function handle(): void
    {
        $this->adminNotifyService->markJobHealthy();
    }
}