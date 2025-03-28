<?php

namespace Aspect\Lib\Blueprint\Dto;

use Aspect\Lib\Support\Interfaces\TransportExporterInterface;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
abstract class Exporter extends Extension implements TransportExporterInterface
{
}