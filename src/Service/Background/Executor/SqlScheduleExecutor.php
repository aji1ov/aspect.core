<?php

namespace Aspect\Lib\Service\Background\Executor;

use Aspect\Lib\Blueprint\DI\Fetch;
use Aspect\Lib\Facade\Queue;
use Aspect\Lib\Table\ScheduleTable;
use Aspect\Lib\Service\Background\Event;
use Aspect\Lib\Service\Console\Color;
use Aspect\Lib\Struct\Mutex;
use Aspect\Lib\Support\Interfaces\JobDispatcherInterface;
use Aspect\Lib\Support\Interfaces\ScheduleExecutorInterface;

class SqlScheduleExecutor implements ScheduleExecutorInterface
{
    use Mutex;

    #[Fetch]
    protected JobDispatcherInterface $dispatcher;
    /**
     * @param Event[] $events
     * @return void
     */
    public function install(array $events): void
    {
        $this->withWaitedLock(function () use ($events) {
            $stored = ScheduleTable::getList()->fetchAll();

            $registered = array_flip(array_column($stored, 'SIGN'));

            $installed = [];
            foreach ($events as $event) {
                $sign = Event::sign($event);

                if(!array_key_exists($sign, $registered)) {
                    notice(Color::GREEN->wrap("Register event: ") . Color::DARK_GREY->wrap($event->getDescription()));
                    ScheduleTable::add([
                        'SIGN' => $sign,
                        'CHECK_TIME' => $event->getCheckTime(time())
                    ]);
                } else if (time() > $stored[$registered[$sign]]['CHECK_TIME']) {
                    notice(Color::BLUE->wrap("Dispatch event: ") . Color::DARK_GREY->wrap($event->getDescription()));
                    $this->dispatcher->dispatch($event->getJob(), Queue::CRON, $stored[$registered[$sign]]['CHECK_TIME']);
                    ScheduleTable::update($stored[$registered[$sign]]['ID'], [
                        'CHECK_TIME' => $event->getCheckTime(time())
                    ]);
                }

                $installed[] = $sign;
            }

            foreach ($stored as $entity) {
                if(!in_array($entity['SIGN'], $installed, true)) {
                    ScheduleTable::delete($entity['ID']);
                    notice(Color::RED->wrap("Remove event: ") . Color::DARK_GREY->wrap($entity['SIGN']));
                }

            }

        });
    }
}