<?php

namespace Aspect\Lib\Support\Interfaces;

use Aspect\Lib\Transport\Transportable;

/**
 * @method serialize(Transportable $dto): mixed
 * @method fill($input): Dto
 */
interface DataTransportSerializerInterface
{
}