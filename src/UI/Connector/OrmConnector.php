<?php

namespace Aspect\Lib\UI\Connector;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;

class OrmConnector implements ConnectorInterface
{
    /**
     * @var class-string<DataManager> $ormEntityClass
     */
    private string $ormEntityClass;

    private ?array $filter = [];

    public function __construct(string $ormEntityClass)
    {
        assert(is_a($ormEntityClass, DataManager::class, true));
        $this->ormEntityClass = $ormEntityClass;
    }

    public function filter(array $filter): static
    {
        $this->filter = $filter;
        return $this;
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function getCount(Query $query): int
    {
        $filter = $query->getFilter();
        if ($this->filter) {
            $filter = array_merge($filter, $this->filter);
        }
        return $this->ormEntityClass::getCount($filter);
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public function getRow(Query $query): iterable
    {
        $queryConnection = [];
        $filter = [];

        if ($queryFilter = $query->getFilter()) {
            $filter = array_merge($filter, $queryFilter);
        }
        if ($this->filter) {
            $filter = array_merge($filter, $this->filter);
        }

        if ($filter) {
            $queryConnection['filter'] = $filter;
        }

        if ($sort = $query->getSort()) {
            $queryConnection['order'] = $sort;
        }

        if ($offset = $query->getOffset()) {
            $queryConnection['offset'] = $offset;
        }

        if (($limit = $query->getLimit()) && $limit > 0) {
            $queryConnection['limit'] = $limit;
        }

        return $this->ormEntityClass::getList($queryConnection)->getIterator();
    }
}