<?php

namespace Aspect\Lib\Transport;

interface TransportInterface
{
    public function toArray(Transportable $dto): array;

    /**
     * @template T
     * @param array $source
     * @param class-string<T> $dto
     * @return T
     */
    public function fromArray(array $source, string $dto): Transportable;
}