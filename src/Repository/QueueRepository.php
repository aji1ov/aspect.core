<?php

namespace Aspect\Lib\Repository;

use Aspect\Lib\Entity\QueueEntity;
use Aspect\Lib\Service\Repository\BitrixD7Repository;
use Aspect\Lib\Table\QueueTable;

/**
 * @extends BitrixD7Repository<QueueEntity>
 */
class QueueRepository extends BitrixD7Repository
{

    public function __construct()
    {
        parent::__construct(QueueEntity::class);
    }


    protected function getDataManager(): string
    {
        return QueueTable::class;
    }
}