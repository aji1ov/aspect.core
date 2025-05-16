<?php

namespace Aspect\Lib\Transport\Blueprint;

use Aspect\Lib\Support\Interfaces\ConverterInterface;
use Aspect\Lib\Transport\TransportInterface;

class RawConverter implements ConverterInterface
{

    public function enrich(ExtensionProperty $property, array $source, mixed $object, TransportInterface $transport): void
    {
        $property->getProperty()->setValue($object, $source['~'.$property->getRemoteKey()] ?? $source[$property->getRemoteKey()]);
    }

    public function export(ExtensionProperty $property, mixed $object, array &$source, TransportInterface $transport): void
    {
        $source[$property->getRemoteKey()] = $property->getProperty()->getValue($object);
    }
}