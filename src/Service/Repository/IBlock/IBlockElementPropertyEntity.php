<?php

namespace Aspect\Lib\Service\Repository\IBlock;


use Aspect\Lib\Blueprint\Dto\Key;
use Aspect\Lib\Transport\Dto;
use Bitrix\Iblock\PropertyTable;

class IBlockElementPropertyEntity extends IBlockPropertyEntity
{
    #[Key('PROPERTY_VALUE_ID')]
    public int|array|null $valueId = null;

    #[Key('VALUE')]
    public string|int|float|array|null $value = null;

    #[Key('VALUE_ENUM')]
    public string|int|float|array|null $valueEnum = null;

    #[Key('VALUE_ENUM_ID')]
    public string|int|float|array|null $valueEnumId = null;

    #[Key('VALUE_SORT')]
    public string|int|float|array|null $valueSort = null;

    #[Key('VALUE_XML_ID')]
    public string|int|float|array|null $valueXmlId = null;

    #[Key('DESCRIPTION')]
    public string|int|float|array|null $description = null;
}