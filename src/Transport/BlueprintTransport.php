<?php

namespace Aspect\Lib\Transport;

use ReflectionException;

class BlueprintTransport implements TransportInterface
{

    /**
     * @throws ReflectionException
     */
    public function toArray(Transportable $dto): array
    {
        $result = [];

        $blueprint = $this->getClassBlueprint($dto::class);

        foreach ($blueprint as $property) {
            $property->export($dto, $result, $this);
        }

        return $result;
    }

    /**
     * @template T
     * @param array $source
     * @param class-string<T> $dto
     * @return T
     * @throws ReflectionException
     */
    public function fromArray(array $source, string $dto): Transportable
    {
        $blueprint = $this->getClassBlueprint($dto);
        $dtoObject = $blueprint->newInstance();

        foreach ($blueprint as $property) {
            $property->enrich($source, $dtoObject, $this);
        }

        return $dtoObject;
    }

    /**
     * @throws ReflectionException
     */
    private function getClassBlueprint(string $dto): DtoBlueprint
    {
        return new DtoBlueprint($dto);
    }

}