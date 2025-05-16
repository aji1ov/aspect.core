<?php

namespace Aspect\Lib\Service\Repository\IBlock;

use Aspect\Lib\Blueprint\Dto\Convert;
use Aspect\Lib\Blueprint\Dto\Key;
use Aspect\Lib\Blueprint\Table\Primary;
use Aspect\Lib\Service\Repository\RepositoryEntity;
use Aspect\Lib\Transport\Converter\YesNoConverter;
use Aspect\Lib\Transport\TransportInterface;
use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\Type\DateTime;

class IBlockEntity extends RepositoryEntity
{
    #[Key('ID')]
    #[Primary]
    public ?int $id = null;

    #[Key('TIMESTAMP_X')]
    public ?DateTime $timestampX = null;

    #[Key('IBLOCK_TYPE_ID')]
    public ?string $iblockTypeId = null;

    #[Key('LID')]
    public ?string $lid = null;

    #[Key('CODE')]
    public ?string $code = null;

    #[Key('API_CODE')]
    public ?string $apiCode = null;

    #[Key('REST_ON')]
    #[Convert(new YesNoConverter())]
    public ?bool $restOn = null;

    #[Key('NAME')]
    public ?string $name = null;

    #[Key('ACTIVE')]
    #[Convert(new YesNoConverter())]
    public ?bool $active = null;

    #[Key('SORT')]
    public ?int $sort = null;

    #[Key('LIST_PAGE_URL')]
    public ?string $listPageUrl = null;

    #[Key('DETAIL_PAGE_URL')]
    public ?string $detailPageUrl = null;

    #[Key('SECTION_PAGE_URL')]
    public ?string $sectionPageUrl = null;

    #[Key('CANONICAL_PAGE_URL')]
    public ?string $canonicalPageUrl = null;

    #[Key('PICTURE')]
    public ?int $PictureId = null;

    #[Key('DESCRIPTION')]
    public ?string $description = null;

    #[Key('DESCRIPTION_TYPE')]
    public ?string $descriptionType = null;

    #[Key('XML_ID')]
    public ?string $xmlId = null;
}