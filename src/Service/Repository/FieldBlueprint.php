<?php

namespace Aspect\Lib\Service\Repository;

class FieldBlueprint
{
    private string $dtoName;
    private string $remoteName;

    private array $attributes = [];

    public function __construct(string $dtoName, string $remoteName, array $attributes = [])
    {
        $this->dtoName = $dtoName;
        $this->remoteName = $remoteName;

        $this->attributes = $attributes;
    }

    public function isPrimary(): bool
    {
        return in_array('primary', $this->attributes);
    }

    public function getRemoteKey(): string
    {
        return $this->remoteName;
    }
}