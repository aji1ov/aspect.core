<?php

namespace Aspect\Lib\Transport;

interface Transportable
{
    public function toArray(?TransportInterface $transport = null): array;
    public static function fromArray(array $source, ?TransportInterface $transport = null): static;
}