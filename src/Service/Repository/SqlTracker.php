<?php

namespace Aspect\Lib\Service\Repository;

class SqlTracker
{
    /**
     * @param \Closure $watchIn
     * @return \Bitrix\Main\Diag\SqlTrackerQuery[]
     */
    public static function getQueries(\Closure $watchIn): array
    {
        $connection = \Bitrix\Main\Application::getConnection();
        $tracker = $connection->startTracker();

        $watchIn();

        $connection->stopTracker();

        return $tracker->getQueries();
    }
}