<?php

namespace Aspect\Lib\Struct;

use Aspect\Lib\Blueprint\Ignore;
use Aspect\Lib\Exception\AspectException;

/**
 * @template K
 * @template V
 * @implements \Iterator<K,V>
 */
abstract class FunctionalIterator implements \Iterator
{

    use PrettyPrint;

    #[Ignore]
    private ?FunctionalIteratorEntity $current;

    #[Ignore]
    private int $index = 0;

    public function __construct()
    {
        $this->next();
    }

    #[Ignore]
    abstract protected function nextEntity(int $index): ?FunctionalIteratorEntity;

    /**
     * @return V
     */
    public function current(): mixed
    {
        return $this->current?->getValue();
    }

    public function next(): void
    {
        $this->current = $this->nextEntity($this->index++);
    }

    /**
     * @return K
     */
    public function key(): mixed
    {
        return $this->current->getKey();
    }

    public function valid(): bool
    {
        return isset($this->current);
    }

    #[Ignore]
    public function rewind(): void
    {
    }

    /**
     * @return V
     */
    public function fetchNext(): mixed
    {
        $current = $this->current();
        $this->next();
        return $current;
    }
}