<?php

namespace Aspect\Lib\Transport\Converter;

use Aspect\Lib\Service\Repository\IBlock\IBlockElementPropertyEntity;
use Aspect\Lib\Support\Interfaces\ConverterInterface;
use Aspect\Lib\Transport\Blueprint\ExtensionProperty;
use Aspect\Lib\Transport\TransportInterface;

class ElementPropertyValueConverter implements ConverterInterface
{

    public function enrich(ExtensionProperty $property, array $source, mixed $object, TransportInterface $transport): void
    {
        $property->getProperty()->setValue($object, IBlockElementPropertyEntity::fromArray($source['PROPERTY_'.$property->getRemoteKey()]));
    }

    public function export(ExtensionProperty $property, mixed $object, array &$source, TransportInterface $transport): void
    {
        $source['PROPERTY_'.$property->getRemoteKey()] = $property->getProperty()->getValue($object)->toArray();
    }
}