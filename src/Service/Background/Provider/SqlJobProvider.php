<?php

namespace Aspect\Lib\Service\Background\Provider;

use Aspect\Lib\Table\QueueTable;
use Aspect\Lib\Service\Background\Job;
use Aspect\Lib\Service\Background\Dispatcher\SqlJobDispatcher;
use Aspect\Lib\Struct\Mutex;
use Aspect\Lib\Support\Interfaces\JobProviderInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Throwable;

class SqlJobProvider implements JobProviderInterface
{
    use Mutex;

    private ?array $queues;

    protected SqlJobDispatcher $producer;
    protected LoggerInterface $logger;

    public function __construct(SqlJobDispatcher $producer, LoggerInterface $logger, ?array $queues)
    {
        $this->logger = $logger;
        $this->producer = $producer;
        $this->queues = $queues;
    }

    public function has(int $startAt): bool
    {
        return $this->withWaitedLock(fn () => (bool)QueueTable::getCount($this->getQuery($startAt)));
    }

    public function remove(int $id): void
    {
        $this->withWaitedLock(function() use ($id) {
            QueueTable::delete($id);
        });
    }

    public function freedom(int $id): void
    {
        $this->withWaitedLock(function() use ($id) {
            QueueTable::update($id, ['BUSY' => false]);
        });
    }

    public function next(int $startAt): ?Job
    {
        $job = $this->withWaitedLock(function() use ($startAt) {
           if($entity = QueueTable::getRow(['filter' => $this->getQuery($startAt)])) {
               QueueTable::update($entity['ID'], ['BUSY' => true]);
               return Job::unserialize((int)$entity['ID'], $entity['SERIAL'], $entity['START_AT']);
           }

           return null;
        });

        if(!$job) {
            return null;
        }

        assert(is_a($job, Job::class, true));
        return $job;
    }

    private function getQuery(int $startAt): array
    {
        $filter = [
            '<=START_AT' => $startAt,
            'BUSY' => false
        ];

        if($this->queues) {
            $filter['TAG'] = $this->queues;
        }

        //notice("fetch from queue:", $filter);

        return $filter;
    }

    /**
     * @throws Exception
     */
    public function handle(Job $job): bool
    {
        $successfully = true;
        $startAt = time();
        $errors = [];

        try {
            $job->handle();
        } catch (Throwable $e) {
            $this->logger->error($e);
            $errors = [$e->getMessage()];
            $successfully = false;
        } finally {
            $this->remove($job->getId());

            $duration = time() - $startAt;
            $this->producer->logJob($job, $successfully, $startAt, $duration, $errors);
        }

        return $successfully;
    }
}