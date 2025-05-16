<?php

namespace Aspect\Lib\Service\Repository\IBlock;

use Aspect\Lib\Service\Repository\BitrixD7Repository;
use Bitrix\Iblock\PropertyEnumerationTable;
use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\Loader;

/**
 * @extends BitrixD7Repository<IBlockPropertyEnumEntity>
 */
class IBlockPropertyEnumRepository extends BitrixD7Repository
{

    public function __construct()
    {
        Loader::includeModule('iblock');
        parent::__construct(IBlockPropertyEnumEntity::class);
    }

    protected function getDataManager(): string
    {
        return PropertyEnumerationTable::class;
    }
}