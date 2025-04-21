<?php

namespace Aspect\Lib\Service\Repository\IBlock;


use Aspect\Lib\Blueprint\Dto\Convert;
use Aspect\Lib\Blueprint\Dto\Key;
use Aspect\Lib\Transport\Converter\YesNoConverter;

class IBlockPropertyEntity extends IBlockGenericEntity
{
    #[Key('DEFAULT_VALUE')]
    public string|int|float|array|null $defaultValue = null;

    #[Key('PROPERTY_TYPE')]
    public ?string $propertyType = null;

    #[Key('ROW_COUNT')]
    public ?int $rowCount = null;

    #[Key('COL_COUNT')]
    public ?int $colCount = null;

    #[Key('LIST_TYPE')]
    public ?string $listType = null;

    #[Key('MULTIPLE')]
    #[Convert(new YesNoConverter())]
    public ?bool $multiple = null;

    #[Key('FILE_TYPE')]
    public ?string $fileType = null;

    #[Key('MULTIPLE_CNT')]
    public ?int $multipleCount = null;

    #[Key('LINK_IBLOCK_ID')]
    public ?int $linkIblockId = null;

    #[Key('WITH_DESCRIPTION')]
    #[Convert(new YesNoConverter())]
    public ?bool $withDescription = null;

    #[Key('FILTRABLE')]
    #[Convert(new YesNoConverter())]
    public ?bool $filtrable = null;

    #[Key('IS_REQUIRED')]
    #[Convert(new YesNoConverter())]
    public ?bool $isRequired = null;

    #[Key('VERSION')]
    public ?int $version = null;

    #[Key('USER_TYPE')]
    public ?string $userType = null;

    #[Key('USER_TYPE_SETTINGS')]
    public ?string $userTypeSettings = null;

    #[Key('HINT')]
    public ?string $hint = null;
}