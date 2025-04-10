<?php

namespace Aspect\Lib\Service\Repository;


use Aspect\Lib\Exception\AspectException;

/**
 * @template-covariant T of EntityInterface
 * @extends BaseRepository<T>
 */
abstract class SelectRepository extends BaseRepository
{
    /**
     * @param array|null $filter
     * @return T
     */
    public function getOneBy(?array $filter = null): mixed
    {
        return $this->getBy($filter)->crop(1)->next();
    }

    /**
     * @param array|null $filter
     * @return EntityCollection<T>
     */
    public function getBy(?array $filter = null): EntityCollection
    {
        $query = new RepositorySelectQuery();
        $query->setFilter($filter ?? []);

        return $this->createCollection($query);
    }

    /**
     * @param mixed $primaryValue
     * @return T
     */
    public function getByPrimary(mixed $primaryValue): mixed
    {
        assert(is_a($this->entityClass, EntityInterface::class, true));

        if ($remoteKey = $this->entityClass::blueprint()->primary()?->getRemoteKey()) {
            return $this->getOneBy([
                $remoteKey => $primaryValue
            ]);
        }

        throw new AspectException('Error on fetch entity: unknown entity primary');
    }

    public function getCount(?array $filter): int
    {
        return $this->getBy($filter)->iterate()->count();
    }

    /**
     * @param RepositorySelectQuery $query
     * @return EntityCollection<T>
     */
    private function createCollection(RepositorySelectQuery $query): EntityCollection
    {
        return new EntityCollection($this->find(...), $query);
    }

    abstract protected function find(RepositorySelectQuery $query): EntityIterator;
}