<?php

namespace Aspect\Lib\Service\Repository;

/**
 * @template T of EntityInterface
 * @implements \IteratorAggregate<int, T>
 */
class EntityCollection implements \IteratorAggregate, \ArrayAccess
{
    private \Closure $creator;
    private RepositorySelectQuery $query;

    private ?EntityIterator $iterator = null;

    public function __construct(\Closure $creator, RepositorySelectQuery $query)
    {
        $this->creator = $creator;
        $this->query = $query;
    }

    public function sort(array $sort): static
    {
        $collection = clone $this;
        $collection->query->setSort($sort);

        return $collection;
    }

    /**
     * @param int $limit
     * @return EntityCollection<T>
     */
    public function crop(int $limit): EntityCollection
    {
        return $this->slice(0, $limit);
    }

    /**
     * @param int $offset
     * @return EntityCollection<T>
     */
    public function shift(int $offset): EntityCollection
    {
        return $this->slice($offset);
    }

    /**
     * @param int|null $offset
     * @param int|null $limit
     * @return EntityCollection<T>
     */
    public function slice(?int $offset = null, ?int $limit = null): EntityCollection
    {
        if(!$offset && !$limit) {
            return $this;
        }

        $collection = clone $this;
        if($offset) {
            $collection->query->setOffset($offset);
        }

        if($limit) {
            $collection->query->setLimit($limit);
        }

        return $collection;
    }

    /**
     * @return EntityIterator<T>
     */
    public function getIterator(): EntityIterator
    {
        if(!$this->iterator) {
            $creator = $this->creator;
            $this->iterator = $creator($this->query);
        }

        return $this->iterator;
    }

    /**
     * @return EntityIterator<T>
     */
    public function iterate(): EntityIterator
    {
        return $this->getIterator();
    }

    /**
     * @return array<mixed,T>
     */
    public function map(callable $handler): array
    {
        $result = [];
        foreach ($this->iterate() as $item)
        {
            $result[$handler($item)] = $item;
        }

        return $result;
    }

    public function prefetch(): EntityIterator
    {
        $iterator = $this->iterate();
        return new EntityIterator(
            new \ArrayIterator(iterator_to_array($iterator)),
            $iterator->count(),
            fn ($entity) => $entity
        );
    }

    /**
     * @return T
     */
    public function next(): mixed
    {
        return $this->getIterator()->fetchNext();
    }

    public function offsetExists(mixed $offset): bool
    {
        return !!$this->offsetGet($offset);
    }

    /**
     * @param int $offset
     * @return T
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->slice($offset, 1)->next();
    }

    public function offsetSet(mixed $offset, mixed $value): void{}
    public function offsetUnset(mixed $offset): void{}
}