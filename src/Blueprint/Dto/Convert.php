<?php

namespace Aspect\Lib\Blueprint\Dto;

use Aspect\Lib\Support\Interfaces\TransportEnricherInterface;
use Aspect\Lib\Support\Interfaces\TransportExporterInterface;
use Aspect\Lib\Transport\Blueprint\ExtensionProperty;
use Aspect\Lib\Transport\Converter\ConverterInterface;
use Aspect\Lib\Transport\TransportInterface;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Convert extends Extension implements TransportEnricherInterface, TransportExporterInterface
{
    private ConverterInterface $converter;
    public function __construct(ConverterInterface $converter) {
        $this->converter = $converter;
    }

    public function enrich(ExtensionProperty $property, array $source, mixed $object, TransportInterface $transport): void
    {
        $this->converter->enrich($property, $source, $object, $transport);
    }

    public function export(ExtensionProperty $property, mixed $object, array &$source, TransportInterface $transport): void
    {
        $this->converter->export($property, $object, $source, $transport);
    }
}