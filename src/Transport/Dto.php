<?php

namespace Aspect\Lib\Transport;

use Aspect\Lib\Application;
use Protobuf\Exception;

abstract class Dto implements Transportable
{
    private static function withTransport(?TransportInterface $transport = null): TransportInterface
    {
        if(!$transport) {
            $transport = Application::getInstance()->get(TransportInterface::class);
        }

        return $transport;
    }

    public function toArray(?TransportInterface $transport = null): array
    {
        return static::withTransport($transport)->toArray($this);
    }

    public static function fromArray(array $source, ?TransportInterface $transport = null): static
    {
        return static::withTransport($transport)->fromArray($source, static::class);
    }
}