<?php

namespace Aspect\Lib\Transport\Converter;

use Aspect\Lib\Support\Interfaces\ConverterInterface;
use Aspect\Lib\Transport\Blueprint\ExtensionProperty;
use Aspect\Lib\Transport\TransportInterface;
use Bitrix\Main\Type\DateTime;

class BitrixDateTimeConverter implements ConverterInterface
{

    public function enrich(ExtensionProperty $property, array $source, mixed $object, TransportInterface $transport): void
    {
        $property->getProperty()->setValue($object, DateTime::createFromText($source[$property->getRemoteKey()]));
    }

    public function export(ExtensionProperty $property, mixed $object, array &$source, TransportInterface $transport): void
    {
        $dt = $property->getProperty()->getValue($object);
        if($dt) {
            assert($dt instanceof DateTime);
            $source[$property->getRemoteKey()] = $dt->toString();
        }
    }
}