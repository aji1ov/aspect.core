<?php

namespace Aspect\Lib\Service\Background\Dispatcher;

use Aspect\Lib\Application;
use Aspect\Lib\Blueprint\DI\Fetch;
use Aspect\Lib\Context;
use Aspect\Lib\Table\JobLogTable;
use Aspect\Lib\Table\QueueTable;
use Aspect\Lib\Service\Background\Job;
use Aspect\Lib\Service\Background\JobInfo;
use Aspect\Lib\Service\Background\Provider\SqlJobProvider;
use Aspect\Lib\Service\Console\Color;
use Aspect\Lib\Support\Interfaces\JobDispatcherInterface;
use Aspect\Lib\Support\Interfaces\JobProviderInterface;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Psr\Log\LoggerInterface;

class SqlJobDispatcher implements JobDispatcherInterface
{

    public function dispatch(Job $job, string $queue, int $startAt): void
    {
        QueueTable::add([
            'SERIAL' => $job->serialize(),
            'SIGN' => $job->getSign(),
            'TAG' => $queue,
            'START_AT' => $startAt
        ]);
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function isDefined(Job $job, string $queue): bool
    {
        return !!QueueTable::getCount(['SIGN' => $job->getSign(), 'TAG' => $queue]);
    }

    public function getProvider(?array $queues): JobProviderInterface
    {
        $provider = new SqlJobProvider(
            $this,
            Application::getInstance()->get(LoggerInterface::class),
            $queues
        );
        Application::getInstance()->fetchTo($provider);

        return $provider;
    }

    public function logJob(Job $job, bool $successfully, int $startedAt, int $duration, ?array $errors = [])
    {
        $fields = [
            'NAME' => $job->getName(),
            'SUCCESS' => $successfully,
            'STARTED_AT' => $startedAt,
            'DURATION' => $duration,
        ];

        if ($errors) {
            $fields['ERRORS'] = $errors;
        }

        $warnings = [];
        if (Application::getInstance()->context() !== Context::CRON) {
            $warnings[] = 'Job executed in synchronized context';
        }

        if ($startedAt - $job->getPlannedAt() > 60) {
            $warnings[] = 'Job started too late (delayed: ' . ($startedAt - $job->getPlannedAt()) . ' secs)';
        }

        if ($warnings) {
            $fields['WARNINGS'] = $warnings;

            foreach ($warnings as $warning) {
                notice(Color::LIGHT_GREY->wrap($warning));
            }
        }

        JobLogTable::add($fields);
    }

    /**
     * @param array|null $queues
     * @return JobInfo[]
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getInfo(?array $queues): array
    {
        $result = [];
        $filter = $queues ? ['TAG' => $queues] : [];
        $iterator = QueueTable::getList(['filter' => $filter]);
        foreach ($iterator->getIterator() as $entity) {

            $job = Job::unserialize($entity['ID'], $entity['SERIAL'], $entity['START_AT']);
            $info = new JobInfo();
            $info->setName($job->getName());
            $info->setSign($entity['SIGN']);
            $info->setQueue($entity['TAG']);
            $info->setIsBusy($entity['BUSY']);
            $info->setStartAt($entity['START_AT']);
            $result[] = $info;
        }

        return $result;
    }


}