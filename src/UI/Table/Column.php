<?php

namespace Aspect\Lib\UI\Table;

use Closure;

class Column extends TableColumn implements TableHeadInterface
{
    private bool $defaultShow = true;
    private ?Closure $renderHandler;
    private ?string $sorted = null;
    private ?Filter $filter = null;

    public function __construct(string $key)
    {
        parent::__construct($key);
        $this->renderHandler = fn($row) => $row[$key];
    }

    public static function make(string $key): static
    {
        return new Column($key);
    }

    public function defaultHidden(): static
    {
        $this->defaultShow = false;
        return $this;
    }

    public function render(Closure $renderHandler): static
    {
        $this->renderHandler = $renderHandler;
        return $this;
    }

    public function isDefaultShow(): bool
    {
        return $this->defaultShow;
    }


    public function sort(?string $key = null): static
    {
        $this->sorted = $key ?? $this->getKey();
        return $this;
    }

    public function filter(Filter $filter): static
    {
        if (!$filter->getKey()) {
            $filter->setKey($this->getKey());
        }

        $this->filter = $filter;
        return $this;
    }

    public function toArray(): array
    {
        $columnOptions = [
            "id" => $this->getKey(),
            "name" => $this->getTitle(),
            "default" => $this->isDefaultShow()
        ];

        if ($this->sorted) {
            $columnOptions['sort'] = $this->sorted;
        }

        return $columnOptions;
    }

    public function getRenderValue(array $row): mixed
    {
        $render = $this->renderHandler;
        return $render($row);
    }

    public function getFilter(): ?Filter
    {
        return $this->filter;
    }
}