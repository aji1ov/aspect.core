<?php

namespace Aspect\Lib\Service\Repository\IBlock;


use Aspect\Lib\Exception\AspectException;
use Aspect\Lib\Service\Repository\CrudRepository;
use Aspect\Lib\Service\Repository\EntityInterface;
use Aspect\Lib\Service\Repository\EntityIterator;
use Aspect\Lib\Service\Repository\RepositorySelectQuery;
use Aspect\Lib\Struct\ServiceLocator;
use Bitrix\Main\Loader;

/**
 * @template T of EntityInterface
 * @extends CrudRepository<T>
 */
abstract class IBlockElementRepository extends CrudRepository
{

    use ServiceLocator;

    public function __construct(string $entityClass)
    {
        Loader::includeModule('iblock');
        parent::__construct($entityClass);
    }

    public function createOne(EntityInterface $entity): int
    {
        $fields = $entity->toArray();

        $propertyKeys = $this->getEntityClass()::getPropertyKeys();
        foreach ($propertyKeys as $propertyKey) {
            $fields['PROPERTY_VALUES'][$propertyKey] = $fields['PROPERTY_'.$propertyKey]['VALUE'];
            unset($fields['PROPERTY_'.$propertyKey]);
        }

        $creator = new \CIBlockElement();
        $primary = $creator->Add($fields);

        if ($error = $creator->LAST_ERROR) {
            throw new AspectException("Error on create entity: " . $error);
        }

        return $primary;
    }

    public function updateOne(EntityInterface $entity): int
    {
        $fields = $entity->toArray();

        if ($primary = $this->splicePrimary($fields)) {
            $propertyKeys = $this->getEntityClass()::getPropertyKeys();
            $updateProperties = [];
            foreach ($propertyKeys as $propertyKey) {
                $updateProperties[$propertyKey] = $fields['PROPERTY_'.$propertyKey]['VALUE'];
                unset($fields['PROPERTY_'.$propertyKey]);
            }

            $updater = new \CIBlockElement();
            $updater->Update($primary, $fields);

            if ($error = $updater->LAST_ERROR) {
                throw new AspectException("Error on update entity: " . $error);
            }

            \CIBlockElement::SetPropertyValuesEx($primary, $fields['IBLOCK_ID'], $updateProperties);

            return $primary;
        }

        throw new AspectException("Error on update entity: unknown entity primary");
    }

    public function deleteOne(EntityInterface $entity): bool
    {
        return 0;
    }

    public function create(EntityInterface ...$entities): array|int
    {
        $result = [];
        foreach ($entities as $entity) {
            $result[] = $this->createOne($entity);
        }

        return count($result) > 1 ? $result : $result[0];
    }

    public function update(EntityInterface ...$entities): array|int
    {
        $result = [];
        foreach ($entities as $entity) {
            $result[] = $this->updateOne($entity);
        }

        return count($result) > 1 ? $result : $result[0];
    }

    public function delete(EntityInterface ...$entities): bool
    {
        foreach ($entities as $entity) {
            $this->deleteOne($entity);
        }

        return true;
    }

    private function makeIterator(\CIBlockResult $result, array $propertyKeys): \Generator
    {
        while($row = $result->GetNext()) {
            $properties = [];
            \CIBlockElement::GetPropertyValuesArray($properties, $row['IBLOCK_ID'], ['ID' => $row['ID']], true, ['CODE' => $propertyKeys]);

            foreach ($properties[$row['ID']] as $propertyKey => $arProperty) {
                $row['PROPERTY_'.$propertyKey] = $arProperty;
            }
            yield $row;
        }
    }

    protected function find(RepositorySelectQuery $query): EntityIterator
    {
        $navigate = [];
        if($query->getLimit()) {
            $navigate['nTopCount'] = $query->getLimit();
        }
        if($query->getOffset()) {
            $navigate['nOffset'] = $query->getOffset();
        }
        $query->updateFilter(['IBLOCK_ID' => $this->iblockId()]);

        $iterator = \CIBlockElement::GetList($query->getSort(), $query->getFilter(), false, $navigate);
        $propertyKeys = $this->getEntityClass()::getPropertyKeys();

        return new EntityIterator(
            $this->makeIterator($iterator, array_values($propertyKeys)),
            $iterator->SelectedRowsCount(),
            fn (array $element) => $this->getEntityClass()::fromArray($element)
        );
    }

    private function splicePrimary(&$fields): mixed
    {
        $entityClass = $this->entityClass;
        assert(is_a($entityClass, EntityInterface::class, true));

        if ($primaryKey = $entityClass::blueprint()->primary()?->getRemoteKey()) {
            $primaryValue = $fields[$primaryKey];
            unset($fields[$primaryKey]);

            return $primaryValue;
        }

        return null;
    }

    abstract public function iblockId():int;
}