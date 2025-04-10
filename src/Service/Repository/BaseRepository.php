<?php

namespace Aspect\Lib\Service\Repository;

/**
 * @template-covariant T of EntityInterface
 */
class BaseRepository implements RepositoryInterface
{
    /**
     * @var class-string<T> $entityClass
     */
    protected string $entityClass;

    /**
     * @param class-string<T> $entityClass
     */
    public function __construct(string $entityClass)
    {
        $this->entityClass = $entityClass;
    }

    /**
     * @return class-string<T>
     */
    protected function getEntityClass(): string
    {
        return $this->entityClass;
    }
}