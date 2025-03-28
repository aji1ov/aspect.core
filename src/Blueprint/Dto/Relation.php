<?php

namespace Aspect\Lib\Blueprint\Dto;

use Aspect\Lib\Transport\Blueprint\ExtensionProperty;
use Aspect\Lib\Transport\Transportable;
use Aspect\Lib\Transport\TransportInterface;
use Attribute;

/**
 * @template T of Transportable
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Relation extends Enricher
{
    /**
     * @var class-string<T> $dtoClass
     */
    protected string $dtoClass;

    /**
     * @param class-string<T> $dtoClass
     */
    public function __construct(string $dtoClass)
    {
        $this->dtoClass = $dtoClass;
    }

    public function enrich(ExtensionProperty $property, array $source, mixed $object, TransportInterface $transport): void
    {
        $arrayItems = [];
        foreach ($source[$property->getRemoteKey()] as $arrayKey => $arrayItem) {
            $arrayItems[$arrayKey] = $transport->fromArray($arrayItem, $this->dtoClass);
        }

        $property->getProperty()->setValue($object, $arrayItems);
    }
}