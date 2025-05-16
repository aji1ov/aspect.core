<?php

namespace Aspect\Lib\Service\Repository;

use Aspect\Lib\Exception\AspectException;
use Aspect\Lib\Struct\ServiceLocator;
use Bitrix\Main\ORM\Data\DataManager;
use Exception;

/**
 * @template T of EntityInterface
 * @extends CrudRepository<T>
 */
abstract class BitrixD7Repository extends CrudRepository
{
    use ServiceLocator;

    /**
     * @return class-string<DataManager>
     */
    abstract protected function getDataManager(): string;

    protected function find(RepositorySelectQuery $query): EntityIterator
    {
        $getList = $this->getDataManager()::getList([
            'order' => $query->getSort(),
            'filter' => $query->getFilter(),
            'offset' => $query->getOffset(),
            'limit' => $query->getLimit()
        ]);

        $iterator = $getList->getIterator();
        $iterator->next();

        return new EntityIterator(
            $iterator,
            $getList->getSelectedRowsCount(),
            fn($entity) => $this->getEntityClass()::fromArray($entity)
        );
    }

    /**
     * @param EntityInterface $entity
     * @return int
     * @throws Exception
     */
    public function createOne(EntityInterface $entity): int
    {
        $fields = $entity->toArray();
        $this->splicePrimary($fields);

        $result = $this->getDataManager()::add($fields);

        if (!$result->isSuccess()) {
            throw new AspectException("Error on save entity: " . implode("\n", $result->getErrorMessages()));
        }

        return $result->getId();
    }

    /**
     * @param EntityInterface[] $entities
     * @return int[]|int
     * @throws Exception
     */
    public function create(EntityInterface ...$entities): array|int
    {
        if (count($entities) === 1) {
            return $this->createOne($entities[0]);
        }

        $queue = array_filter($entities, function (EntityInterface $entity) {
            $fields = $entity->toArray($entity);
            $this->splicePrimary($fields);

            return $fields;
        });

        $result = $this->getDataManager()::addMulti($queue);

        if (!$result->isSuccess()) {
            throw new AspectException("Error on save entities: " . implode("\n", $result->getErrorMessages()));
        }

        return $result->getId();
    }

    /**
     * @param EntityInterface $entity
     * @return int
     * @throws Exception
     */
    public function updateOne(EntityInterface $entity): int
    {
        $dataManager = $this->getDataManager();
        $fields = $entity->toArray();
        if ($primary = $this->splicePrimary($fields)) {
            $result = $dataManager::update($primary, $fields);

            if (!$result->isSuccess()) {
                throw new AspectException("Error on update entity: " . implode("\n", $result->getErrorMessages()));
            }

            return $result->getId();
        }

        throw new AspectException("Error on update entity: unknown entity primary");
    }

    /**
     * @param EntityInterface[] $entities
     * @return int[]|int
     * @throws Exception
     */
    public function update(EntityInterface ...$entities): array|int
    {
        if (count($entities) === 1) {
            return $this->updateOne($entities[0]);
        }

        $primaries = [];
        $data = [];

        foreach ($entities as $entity) {
            $fields = $entity->toArray($entity);
            if ($primary = $this->splicePrimary($fields)) {
                $primaries[] = $primary;
                $data[] = $fields;
                continue;
            }

            throw new AspectException("Error on update entities: unknown entity primary");
        }

        $result = $this->getDataManager()::updateMulti($primaries, $data);

        if (!$result->isSuccess()) {
            throw new AspectException("Error on update entities: " . implode("\n", $result->getErrorMessages()));
        }

        return $result->getId();
    }

    public function deleteOne(EntityInterface $entity): bool
    {
        $fields = $entity->toArray();
        if ($primary = $this->splicePrimary($fields)) {
            $result = $this->getDataManager()::delete($primary);
            if (!$result->isSuccess()) {
                throw new AspectException("Error on delete entities: " . implode("\n", $result->getErrorMessages()));
            }

            return true;
        }

        throw new AspectException("Error on delete entity: unknown entity primary");
    }

    public function delete(EntityInterface ...$entities): bool
    {
        foreach ($entities as $entity) {
            $this->deleteOne($entity);
        }

        return true;
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
}