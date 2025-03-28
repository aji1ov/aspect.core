<?php

namespace Aspect\Lib\Blueprint\Dto;

use Aspect\Lib\Support\Interfaces\TransportEnricherInterface;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
abstract class Enricher extends Extension implements TransportEnricherInterface
{
}