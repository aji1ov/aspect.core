<?php

namespace Aspect\Lib\Service\Repository\IBlock;

use Aspect\Lib\Blueprint\Dto\Key;
use Aspect\Lib\Transport\TransportInterface;
use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\Type\DateTime;

abstract class IBlockElementEntity extends IBlockGenericEntity
{
    #[Key('TIMESTAMP_X')]
    public ?DateTime $timestampX = null;

    #[Key('MODIFIED_BY')]
    public ?int $modifiedBy = null;

    #[Key('DATE_CREATE')]
    public ?DateTime $dateCreate = null;

    #[Key('MODIFIED_BY')]
    public ?int $createdBy = null;

    #[Key('IBLOCK_SECTION_ID')]
    public ?int $iblockSectionId = null;

    #[Key('ACTIVE_FROM')]
    public ?DateTime $activeFrom = null;

    #[Key('ACTIVE_TO')]
    public ?DateTime $activeTo = null;

    #[Key('PREVIEW_PICTURE')]
    public ?int $previewPictureId = null;

    #[Key('PREVIEW_TEXT')]
    public ?string $previewText = null;

    #[Key('DETAIL_PICTURE')]
    public ?int $detailPictureId = null;

    #[Key('DETAIL_TEXT')]
    public ?string $detailText = null;

    #[Key('TAGS')]
    public ?string $tags = null;

    #[Key('SHOW_COUNTER')]
    public ?int $showCounter = null;


    public static function getPropertyKeys(): array
    {
        $keys = [];
        $rc = new \ReflectionClass(static::class);

        do {
            foreach ($rc->getProperties() as $property) {
                if ($property->getType() instanceof \ReflectionNamedType && is_a($property->getType()->getName(), IBlockElementPropertyEntity::class, true)) {
                    if ($keyAttributes = $property->getAttributes(Key::class)) {
                        $keyAttribute = $keyAttributes[0]->newInstance();
                        assert($keyAttribute instanceof Key);
                        $keys[$property->name] = $keyAttribute->getName();
                    } else {
                        $keys[$property->name] = $property->name;
                    }
                }
            }
        } while ($rc = $rc->getParentClass());

        return $keys;
    }

    abstract protected static function repositoryClass(): string;

    public function repository(): IBlockElementRepository
    {
        $cls = static::repositoryClass();
        assert(is_a($cls, IBlockElementRepository::class, true));
        return $cls::getInstance();
    }

    public static function new(?IBlockElementEntity $fromArray = null): static
    {
        $entity = $fromArray ?? parent::new();
        $entity->iblockId = $entity->repository()->iblockId();

        $propertyKeys = array_flip(static::getPropertyKeys());
        $propertyIterator = PropertyTable::getList(['filter' => ['IBLOCK_ID' => $entity->iblockId, 'CODE' => $propertyKeys]])->getIterator();
        foreach ($propertyIterator as $arProperty) {
            $entity->{$propertyKeys[$arProperty['CODE']]} = IBlockElementPropertyEntity::fromArray($arProperty);
        }

        return $entity;
    }

    public static function fromArray(array $source, ?TransportInterface $transport = null): static
    {
        $entity = parent::fromArray($source, $transport);
        return static::new($entity);
    }

    public function iblockEntity(): IBlockEntity
    {
        return IBlockRepository::getInstance()->getByPrimary($this->iblockId);
    }

    public function picture(int $pictureId, ?int $width = null, ?int $height = null, ?int $cropType = 2): array
    {
        $fileArray = \CFile::GetFileArray($pictureId);
        if($width) {
            if (!$height) {
                $height = $width;
            }

            $fileArray = \CFile::ResizeImageGet($fileArray, ['width' => $width, 'height' => $height], $cropType);
        }
        return $fileArray;
    }

    public function detailPicture(?int $width = null, ?int $height = null, ?int $cropType = 2): array
    {
        return $this->picture($this->detailPictureId, $width, $height, $cropType);
    }
}