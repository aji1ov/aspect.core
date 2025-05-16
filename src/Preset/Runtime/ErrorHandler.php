<?php

namespace Aspect\Lib\Preset\Runtime;

use Aspect\Lib\Application;
use Aspect\Lib\Service\Logger\IgnitionLogger;
use Aspect\Lib\Support\Interfaces\RuntimeInterface;
use Bitrix\Main\Config\Configuration;
use Closure;
use Spatie\FlareClient\Enums\MessageLevels;
use Spatie\FlareClient\Flare;
use Spatie\FlareClient\Report;
use Spatie\Ignition\Ignition;

class ErrorHandler implements RuntimeInterface
{

    protected function bufferResetMiddleware(Report $report, Closure $next): mixed
    {
        $report->frameworkVersion(SM_VERSION);
        global $APPLICATION;
        $APPLICATION->RestartBuffer();
        return $next($report);
    }

    protected function getDatabaseVersion(): string
    {
        $databaseValues = [];
        foreach (\Bitrix\Main\Application::getInstance()->getConnectionPool()->getConnection()->query('SHOW GLOBAL VARIABLES')->fetchAll() as $row) {
            $databaseValues[$row['Variable_name']] = $row['Value'];
        }
        return implode(" ", [
            $databaseValues['version'],
            $databaseValues['version_comment'],
        ]);
    }

    protected function configureFlare(Flare $flare): void
    {
        global $USER;

        $flare->context('Database', $this->getDatabaseVersion());
        $flare->context('Bitrix', SM_VERSION);

        $flare->group('user', [
            'id' => $USER->GetID(),
            'fullName' => $USER->GetFullName()
        ]);

        $flare->registerMiddleware([
            $this->bufferResetMiddleware(...)
        ]);
    }

    public function onBitrixLoaded(): void
    {

    }

    public function onReady(Application $application): void
    {
        if (Configuration::getInstance()->get('exception_handling')['debug']) {
            $ignition = Ignition::make()
                ->applicationPath($_SERVER['DOCUMENT_ROOT'])
                ->configureFlare($this->configureFlare(...))
                ->register();

            IgnitionLogger::getInstance()->setFlare($ignition->getFlare());
        }
    }
}