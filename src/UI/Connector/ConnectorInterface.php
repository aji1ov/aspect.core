<?php

namespace Aspect\Lib\UI\Connector;

interface ConnectorInterface
{
    public function getCount(Query $query): int;
    public function getRow(Query $query): iterable;
}