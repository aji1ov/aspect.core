<?php

namespace Aspect\Lib\UI\Table;

class Filter
{
    private ?string $key;
    private FilterSimpleType $type;

    public function __construct(?string $key = null, FilterSimpleType $type = FilterSimpleType::TEXT)
    {
        $this->key = $key;
        $this->type = $type;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function setKey(string $key): static
    {
        $this->key = $key;
        return $this;
    }

    public static function text(?string $key = null): static
    {
        return new Filter($key, FilterSimpleType::TEXT);
    }

    public static function number(?string $key = null): static
    {
        return new Filter($key, FilterSimpleType::NUMBER);
    }

    public static function checkbox(?string $key = null): static
    {
        return new Filter($key, FilterSimpleType::CHECKBOX);
    }

    public function getType(): string
    {
        return $this->type->value;
    }

    public function enrich(array $source, array &$filter, array &$liveFilter, ?string $live = null): void
    {
        switch ($this->type) {

            case FilterSimpleType::CHECKBOX:
                $value = $source[$this->getKey()];
                if ($value === 'Y') {
                    $filter[$this->getKey()] = 1;
                } else if ($value === 'N') {
                    $filter[$this->getKey()] = 0;
                }
                break;

            default:
                if ($value = $source[$this->getKey()]) {
                    $filter[$this->getKey()] = $value;
                }
                if ($live) {
                    $liveFilter[] = ['%' . $this->getKey() => $live];
                }
                break;
        }
    }
}