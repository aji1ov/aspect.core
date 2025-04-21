<?php

namespace Aspect\Lib\Service\Repository;

class RepositorySelectQuery
{
    protected array $sort = [];
    protected array $filter = [];
    protected int $offset = 0;
    protected int $limit = 0;

    public function getSort(): array
    {
        return $this->sort;
    }

    public function setSort(array $sort): void
    {
        $this->sort = $sort;
    }

    public function getFilter(): array
    {
        return $this->filter;
    }

    public function setFilter(array $filter): void
    {
        $this->filter = $filter;
    }

    public function updateFilter(array $overrides): void
    {
        $this->filter = array_merge($this->filter, $overrides);
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function setOffset(int $offset): void
    {
        $this->offset = $offset;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }
}