<?php

namespace Aspect\Lib\UI\Table;

class Navigator
{

    private bool $canShowAll = false;
    /**
     * @var int[]|null
     */
    private ?array $paginateSizes = null;
    private bool $showTotal = false;

    public static function make(): static
    {
        return new Navigator();
    }

    public function paginate(int ...$sizes): static
    {
        $this->paginateSizes = $sizes;
        return $this;
    }

    public function showAll(): static
    {
        $this->canShowAll = true;
        return $this;
    }

    public function showTotal(): static
    {
        $this->showTotal = true;
        return $this;
    }

    public function isShowTotal(): bool
    {
        return $this->showTotal;
    }

    /**
     * @return int[]|null
     */
    public function getPaginationSizes(): ?array
    {
        return $this->paginateSizes;
    }
}