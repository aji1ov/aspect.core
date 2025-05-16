<?php

namespace Aspect\Lib\Service\Repository;

/**
 * @template T of EntityInterface
 * @extends SelectRepository<T>
 */
abstract class CrudRepository extends SelectRepository
{
    abstract public function createOne(EntityInterface $entity): int;
    abstract public function updateOne(EntityInterface $entity): int;
    abstract public function deleteOne(EntityInterface $entity): bool;

    /**
     * @param EntityInterface ...$entities
     * @return int[]|int
     */
    abstract public function create(EntityInterface ...$entities): array|int;

    /**
     * @param EntityInterface ...$entities
     * @return int[]|int
     */
    abstract public function update(EntityInterface ...$entities): array|int;

    abstract public function delete(EntityInterface ...$entities): bool;
}