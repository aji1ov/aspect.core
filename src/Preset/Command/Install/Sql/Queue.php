<?php

namespace Aspect\Lib\Preset\Command\Install\Sql;

use Aspect\Lib\Blueprint\DI\Fetch;
use Aspect\Lib\Repository\JobLogTable;
use Aspect\Lib\Repository\QueueTable;
use Aspect\Lib\Service\Console\Color;
use Aspect\Lib\Service\Console\Command;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\DB\Connection;
use Bitrix\Main\SystemException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Queue extends Command
{
    #[Fetch]
    protected Connection $sql;

    const TABLES = [QueueTable::class, JobLogTable::class];

    /**
     * @throws SystemException
     * @throws ArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        foreach (static::TABLES as $table) {
            if($this->sql->isTableExists($table::getTableName())) {
                $this->sql->dropTable($table::getTableName());
            }

            $table::getEntity()->createDbTable();
        }
    }

    public static function getDescription(): string
    {
        return 'Create '.Color::BLUE->wrap('`aspect_queue`').' sql table';
    }

    public static function structure(): array
    {
        return [];
    }
}