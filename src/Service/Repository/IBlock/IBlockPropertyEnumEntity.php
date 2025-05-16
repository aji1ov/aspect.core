<?php

namespace Aspect\Lib\Service\Repository\IBlock;


use Aspect\Lib\Blueprint\Dto\Convert;
use Aspect\Lib\Blueprint\Dto\Key;
use Aspect\Lib\Blueprint\Table\Primary;
use Aspect\Lib\Service\Repository\RepositoryEntity;
use Aspect\Lib\Transport\Converter\YesNoConverter;

class IBlockPropertyEnumEntity extends RepositoryEntity
{
    #[Primary]
    #[Key('ID')]
    public ?int $id = null;

    #[Key('PROPERTY_ID')]
    public ?int $propertyId = null;

    #[Key('VALUE')]
    public ?string $value = null;

    #[Convert(new YesNoConverter())]
    #[Key('DEF')]
    public ?bool $def = null;

    #[Key('SORT')]
    public ?int $sort = null;

    #[Key('XML_ID')]
    public ?string $xmlId = null;

    #[Key('TMP_ID')]
    public ?string $tmpId = null;
}