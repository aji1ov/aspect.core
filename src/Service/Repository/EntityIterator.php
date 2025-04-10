<?php

namespace Aspect\Lib\Service\Repository;

use Aspect\Lib\Blueprint\Ignore;
use Aspect\Lib\Blueprint\Pretty;
use Aspect\Lib\Struct\FunctionalIterator;
use Aspect\Lib\Struct\FunctionalIteratorEntity;
use Aspect\Lib\Struct\PrettyPrint;

/**
 * @template V of EntityInterface
 * @extends FunctionalIterator<integer, V>
 */
class EntityIterator extends FunctionalIterator implements \Countable
{

    #[Ignore]
    private \Iterator $source;

    #[Pretty]
    private int $count;

    #[Ignore]
    private \Closure $convert;

    public function __construct(\Iterator $source, int $count, \Closure $convert)
    {
        $this->source = $source;
        $this->count = $count;
        $this->convert = $convert;
        parent::__construct();
    }

    #[Ignore]
    protected function nextEntity(int $index): ?FunctionalIteratorEntity
    {
        $convert = $this->convert;
        
        $raw = $this->source->current();
        $this->source->next();

        return !$raw
            ? null
            : new FunctionalIteratorEntity(
                $this->source->key(),
                $convert($raw)
            );
    }

    public function count(): int
    {
        return $this->count;
    }

    /**
     * @return V
     */
    public function current(): mixed
    {
        return parent::current();
    }

    public function buffered(int $size): EntityIterator
    {
        $buffer = [];
        for ($i = 0; $i <= $size - 1; $i++) {
            $next = $this->fetchNext();
            if(!$next) {
                break;
            }
            $buffer[] = $next;
        }

        return new EntityIterator(
            new \ArrayIterator($buffer),
            count($buffer),
            fn ($entity) => $entity
        );
    }
}