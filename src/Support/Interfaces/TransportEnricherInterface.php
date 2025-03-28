<?php

namespace Aspect\Lib\Support\Interfaces;

use Aspect\Lib\Transport\Blueprint\ExtensionProperty;
use Aspect\Lib\Transport\TransportInterface;

interface TransportEnricherInterface
{
    public function enrich(ExtensionProperty $property, array $source, mixed $object, TransportInterface $transport): void;
}