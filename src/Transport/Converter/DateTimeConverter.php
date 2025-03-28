<?php

namespace Aspect\Lib\Transport\Converter;

use Aspect\Lib\Transport\Blueprint\ExtensionProperty;
use Aspect\Lib\Transport\TransportInterface;
use Bitrix\Main\Type\DateTime;

class DateTimeConverter implements ConverterInterface
{
    private string $format;
    public function __construct(string $format)
    {
        $this->format = $format;
    }

    public function enrich(ExtensionProperty $property, array $source, mixed $object, TransportInterface $transport): void
    {
        $property->getProperty()->setValue($object, \DateTime::createFromFormat($this->format, $source[$property->getRemoteKey()]));
    }

    public function export(ExtensionProperty $property, mixed $object, array &$source, TransportInterface $transport): void
    {
        $datetimeValue = $property->getProperty()->getValue($object);
        assert($datetimeValue instanceof DateTime);
        $source[$property->getRemoteKey()] = $datetimeValue->format($this->format);
    }
}