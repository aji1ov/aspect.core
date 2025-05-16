<?php

namespace Aspect\Lib\Service\Repository\IBlock;

use Aspect\Lib\Service\Repository\BitrixD7Repository;
use Bitrix\Iblock\IblockTable;
use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\Loader;

/**
 * @extends BitrixD7Repository<IBlockEntity>
 */
class IBlockRepository extends BitrixD7Repository
{

    public function __construct()
    {
        Loader::includeModule('iblock');
        parent::__construct(IBlockEntity::class);
    }

    public function getByCode(string $code): ?IBlockEntity
    {
        return $this->getOneBy(['CODE' => $code]);
    }

    protected function getDataManager(): string
    {
        return IblockTable::class;
    }
}