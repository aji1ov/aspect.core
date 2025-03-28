<?php

namespace Aspect\Lib\UI;

use Aspect\Lib\Render\ComponentView;
use Aspect\Lib\UI\Connector\ConnectorInterface;
use Aspect\Lib\UI\Enricher\BitrixUiGridEnricher;
use Aspect\Lib\UI\Table\Action;
use Aspect\Lib\UI\Table\ButtonInterface;
use Aspect\Lib\UI\Table\Navigator;
use Aspect\Lib\UI\Table\TableColumnInterface;
use Aspect\Lib\UI\Table\TableEnricherInterface;
use Aspect\Lib\UI\Table\TableRowInterface;

class Table extends ComponentView
{
    private string $gridId;
    /**
     * @var TableColumnInterface[]|null $columns
     */
    private ?array $columns = null;
    /**
     * @var ButtonInterface[]|null
     */
    private ?array $buttons = null;

    private ?ConnectorInterface $connector;
    private ?Navigator $navigator = null;

    private TableEnricherInterface $enricher;

    public function __construct(string $gridId)
    {
        $this->gridId = preg_replace("/(\W)/", "", $gridId);
        $this->enricher = new BitrixUiGridEnricher();
    }

    public function buttons(ButtonInterface ...$buttons): static
    {
        $this->buttons = $buttons;
        return $this;
    }

    public function columns(TableColumnInterface ...$columns): static
    {
        $this->columns = $columns;
        return $this;
    }

    public function connect(ConnectorInterface $connector): static
    {
        $this->connector = $connector;
        return $this;
    }

    public function navigator(Navigator $navigator): static
    {
        $this->navigator = $navigator;
        return $this;
    }

    public function getGridId(): string
    {
        return $this->gridId;
    }

    public function getConnector(): ?ConnectorInterface
    {
        return $this->connector;
    }

    public function getColumns(): ?array
    {
        return $this->columns;
    }

    public function getNavigator(): ?Navigator
    {
        return $this->navigator;
    }

    /**
     * @return ButtonInterface[]|null
     */
    public function getButtons(): ?array
    {
        return $this->buttons;
    }

    protected function getComponentName(): string
    {
        return 'aspect:ui.table';
    }

    protected function getComponentTemplate(): string
    {
        return '';
    }

    protected function getComponentParams(): array
    {
        return $this->enricher->getTableComponentParameters($this);
    }
}