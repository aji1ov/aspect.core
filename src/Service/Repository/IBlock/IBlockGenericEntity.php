<?php

namespace Aspect\Lib\Service\Repository\IBlock;


use Aspect\Lib\Blueprint\Dto\Convert;
use Aspect\Lib\Blueprint\Dto\Key;
use Aspect\Lib\Blueprint\Table\Primary;
use Aspect\Lib\Service\Repository\RepositoryEntity;
use Aspect\Lib\Transport\Converter\YesNoConverter;

class IBlockGenericEntity extends RepositoryEntity
{
    #[Key('ID')]
    #[Primary]
    public ?int $id = null;

    #[Key('IBLOCK_ID')]
    public int $iblockId;

    #[Key('NAME')]
    public ?string $name = null;

    #[Key('ACTIVE')]
    #[Convert(new YesNoConverter())]
    public ?bool $active = null;

    #[Key('SORT')]
    public ?int $sort = null;

    #[Key('CODE')]
    public ?string $code = null;

    #[Key('XML_ID')]
    public ?string $xmlId = null;

}