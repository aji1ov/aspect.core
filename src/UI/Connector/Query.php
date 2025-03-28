<?php

namespace Aspect\Lib\UI\Connector;

class Query
{
    private ?array $filter = null;
    private ?array $sort = null;
    private int $offset = 0;
    private int $limit = -1;

    public function getFilter(): array
    {
        return $this->filter;
    }

    public function setFilter(array $filter): void
    {
        $this->filter = $filter;
    }

    public function getSort(): array
    {
        return $this->sort;
    }

    public function setSort(array $sort): void
    {
        $this->sort = $sort;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function paginate(int $offset, int $limit): void
    {
        $this->offset = $offset;
        $this->limit = $limit;
    }
}