<?php

namespace Aspect\Lib\Support\Interfaces;

use Aspect\Lib\Transport\Blueprint\ExtensionProperty;
use Aspect\Lib\Transport\TransportInterface;

interface TransportExporterInterface
{
    public function export(ExtensionProperty $property, mixed $object, array &$source, TransportInterface $transport): void;
}