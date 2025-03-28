<?php

namespace Aspect\Lib\Preset\Command\Orm\Table;

use Aspect\Lib\Blueprint\DI\Fetch;
use Aspect\Lib\Service\Console\Argument;
use Aspect\Lib\Service\Console\Background;
use Aspect\Lib\Service\Console\Color;
use Aspect\Lib\Service\Console\Command;
use Aspect\Lib\Service\Console\Option;
use Bitrix\Main\DB\Connection;
use Bitrix\Main\Entity\DataManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Create extends Command
{

    #[Fetch]
    protected Connection $orm;

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $tableClass = $input->getArgument('table');
        $drop = $input->getOption('drop');

        assert(is_a($tableClass, DataManager::class, true));

        if($this->orm->isTableExists($tableClass::getTableName())) {
            if($drop) {
                $this->orm->dropTable($tableClass::getTableName());
            } else {
                return;
            }
        }

        $tableClass::getEntity()->createDbTable();
    }

    public static function getDescription(): string
    {
        return 'Create sql table from ORM reference class';
    }

    public static function structure(): array
    {
        return [
            static::argument('table', Argument::REQUIRED, 'Path to ORM table class '.Background::LIGHT_RED->wrap("(not table name in sql!)")),
            static::option('drop', 'd', Option::NONE, 'Drop table before creating if exists')
        ];
    }
}