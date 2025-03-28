<?php

namespace Aspect\Lib\Transport\Blueprint;

use Aspect\Lib\Blueprint\Dto\Key;
use Aspect\Lib\Transport\TransportInterface;
use ReflectionProperty;
use ReflectionType;

abstract class BlueprintProperty
{
    protected string $remoteKey;
    protected ReflectionProperty $property;
    protected ReflectionType $type;

    public function __construct(ReflectionProperty $property, ReflectionType $type)
    {
        $this->property = $property;
        $this->type = $type;

        $this->makeKeys();
    }

    private function makeKeys(): void
    {
        $remoteKey = $this->property->getName();
        if ($keyAttributes = $this->property->getAttributes(Key::class)) {
            foreach ($keyAttributes as $keyAttribute) {
                $key = $keyAttribute->newInstance();
                assert($key instanceof Key);

                $remoteKey = $key->getName();
                break;
            }
        }

        $this->remoteKey = $remoteKey;
    }


    public function getRemoteKey(): string
    {
        return $this->remoteKey;
    }

    public function getProperty(): ReflectionProperty
    {
        return $this->property;
    }

    public function getType(): ReflectionType
    {
        return $this->type;
    }

    abstract public function export(mixed $object, array &$source, TransportInterface $transport): void;

    abstract public function enrich(array $source, mixed $object, TransportInterface $transport): void;
}