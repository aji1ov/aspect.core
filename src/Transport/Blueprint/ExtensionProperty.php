<?php

namespace Aspect\Lib\Transport\Blueprint;

use Aspect\Lib\Blueprint\Dto\Enricher;
use Aspect\Lib\Blueprint\Dto\Extension;
use Aspect\Lib\Support\Interfaces\TransportEnricherInterface;
use Aspect\Lib\Support\Interfaces\TransportExporterInterface;
use Aspect\Lib\Transport\TransportInterface;
use ReflectionProperty;
use ReflectionType;

class ExtensionProperty extends BlueprintProperty
{

    /**
     * @var Extension[] $extensions
     */
    private array $extensions;

    public function __construct(ReflectionProperty $property, ReflectionType $type, array $extension)
    {
        $this->extensions = $extension;
        parent::__construct($property, $type);
    }

    public function export(mixed $object, array &$source, TransportInterface $transport): void
    {
        foreach ($this->extensions as $extension) {
            if ($extension instanceof TransportExporterInterface) {
                $extension->export($this, $object, $source, $transport);
            }
        }
    }

    public function enrich(array $source, mixed $object, TransportInterface $transport): void
    {
        foreach ($this->extensions as $extension) {
            if ($extension instanceof TransportEnricherInterface) {
                $extension->enrich($this, $source, $object, $transport);
            }
        }
    }
}