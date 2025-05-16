<?php

namespace Aspect\Lib\Service\Repository\IBlock;

use Aspect\Lib\Service\Repository\BitrixD7Repository;
use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\Loader;

/**
 * @extends BitrixD7Repository<IBlockPropertyEntity>
 */
class IBlockPropertyRepository extends BitrixD7Repository
{

    public function __construct()
    {
        Loader::includeModule('iblock');
        parent::__construct(IBlockPropertyEntity::class);
    }

    protected function getDataManager(): string
    {
        return PropertyTable::class;
    }
}