<?php

namespace Aspect\Lib\Transport\Converter;

use Aspect\Lib\Support\Interfaces\ConverterInterface;
use Aspect\Lib\Transport\Blueprint\ExtensionProperty;
use Aspect\Lib\Transport\TransportInterface;

class YesNoConverter implements ConverterInterface
{
    public function enrich(ExtensionProperty $property, array $source, mixed $object, TransportInterface $transport): void
    {
        $property->getProperty()->setValue($object, $source[$property->getRemoteKey()] === 'Y');
    }

    public function export(ExtensionProperty $property, mixed $object, array &$source, TransportInterface $transport): void
    {
        $source[$property->getRemoteKey()] = $property->getProperty()->getValue($object) ? 'Y' : 'N';
    }
}