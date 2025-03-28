<?php

namespace Aspect\Lib\UI\Enricher;

use Aspect\Lib\Support\Interfaces\ViewInterface;
use Aspect\Lib\UI\Connector\Query;
use Aspect\Lib\UI\Table;
use Aspect\Lib\UI\Table\Action;
use Aspect\Lib\UI\Table\Column;
use Aspect\Lib\UI\Table\TableColumn;
use Aspect\Lib\UI\Table\TableEnricherInterface;
use Aspect\Lib\UI\Table\TableHeadInterface;
use Aspect\Lib\UI\Table\TableRowInterface;
use Bitrix\Main\Grid\Context;
use Bitrix\Main\Grid\Options;
use Bitrix\Main\UI\PageNavigation;

class BitrixUiGridEnricher implements TableEnricherInterface
{

    public function getTableComponentParameters(Table $table): array
    {
        return [
            'FILTER' => $this->getFilterComponentParameters($table),
            'GRID' => $this->getGridComponentParameters($table),
            'BUTTONS' => $this->getHeadButtons($table),
        ];
    }

    private function getHeadButtons(Table $table): array
    {
        $renders = [];
        if ($buttons = $table->getButtons()) {
            foreach ($buttons as $button) {
                if ($button instanceof ViewInterface) {
                    $renders[] = $button->render();
                }
            }
        }

        return $renders;
    }

    private function getFilterComponentParameters(Table $table): array
    {
        $filters = [];

        foreach ($table->getColumns() as $column) {
            if ($column instanceof Column) {
                if ($filter = $column->getFilter()) {
                    $filters[] = [
                        'id' => $filter->getKey() ?? $column->getKey(),
                        'name' => $column->getTitle(),
                        'type' => $filter->getType()
                    ];
                }
            }
        }

        return [
            'FILTER_ID' => $table->getGridId(),
            'GRID_ID' => $table->getGridId(),
            'FILTER' => $filters,
            'ENABLE_LIVE_SEARCH' => true,
            'ENABLE_LABEL' => true
        ];
    }

    private function getGridComponentParameters(Table $table): array
    {
        $gridOptions = $this->getGridOptions($table);
        $pageNavigation = $this->getPageNavigation($table, $gridOptions);

        $query = $this->getQuery($table, $gridOptions, $pageNavigation);

        $navigator = $table->getNavigator();

        $gridParams = [
            'GRID_ID' => $table->getGridId(),
            'AJAX_ID' => \CAjax::getComponentID('bitrix:main.ui.grid', '.default', ''),
            'NAV_OBJECT' => $pageNavigation,
            'ALLOW_COLUMNS_SORT' => true,
            'ALLOW_SORT' => true,
        ];

        if ($navigator) {
            if ($navigator->isShowTotal() && $connector = $table->getConnector()) {
                $total = $connector->getCount($query);

                $gridParams['TOTAL_ROWS_COUNT'] = $total;
                $pageNavigation->setRecordCount($total);
            }

            if ($sizes = $navigator->getPaginationSizes()) {
                $gridParams['SHOW_PAGESIZE'] = true;
                $gridParams['SHOW_NAVIGATION_PANEL'] = true;
                $gridParams['PAGE_SIZES'] = array_map(fn($size) => ['NAME' => $size, 'VALUE' => $size], $sizes);

                $pageNavigation->setPageSizes($sizes);
            }
        }

        $gridParams = array_merge($gridParams, [
            'COLUMNS' => $this->getColumnsData($table),
            'ROWS' => $this->getRows($table, $query)
        ]);

        if (Context::isInternalRequest()) {
            global $APPLICATION;
            $APPLICATION->RestartBuffer();
            $gridParams['AJAX'] = true;
        }

        return $gridParams;
    }

    private function getQueryFilter(Table $table): array
    {
        $filter = [];
        $liveFilter = [];
        $filterOption = new \Bitrix\Main\UI\Filter\Options($table->getGridId());
        $filterSource = $filterOption->getFilter();

        $live = $filterSource['FIND'];

        foreach ($table->getColumns() as $column) {
            if ($column instanceof Column) {
                if ($columnFilter = $column->getFilter()) {
                    $columnFilter->enrich($filterSource, $filter, $liveFilter, $live);
                }
            }
        }

        if ($liveFilter) {
            $liveFilter['LOGIC'] = 'OR';
            $filter[] = $liveFilter;
        }

        return $filter;
    }

    private function getQuery(Table $table, Options $options, PageNavigation $pageNavigation): Query
    {
        $query = new Query();
        $query->setFilter($this->getQueryFilter($table));
        $query->setSort($options->GetSorting(['vars' => ['by' => 'by', 'order' => 'order']])['sort']);

        $pageSize = $options->GetNavParams()['nPageSize'];
        $currentPage = $pageNavigation->getCurrentPage();

        if ($pageNavigation->allRecordsShown()) {
            $query->paginate(0, -1);
        } else {
            $query->paginate($pageSize * ($currentPage - 1), $pageSize);
        }

        return $query;
    }

    private function getPageNavigation(Table $table, Options $options): PageNavigation
    {
        $nav = new PageNavigation($table->getGridId());
        $nav->allowAllRecords(true)
            ->setPageSize($options->GetNavParams()['nPageSize'])
            ->initFromUri();

        return $nav;
    }

    private function getGridOptions(Table $table): Options
    {
        return new Options($table->getGridId());
    }

    private function getColumnsData(Table $table): array
    {

        $columns = [];

        foreach ($table->getColumns() as $column) {
            assert($column instanceof TableColumn);

            if ($column instanceof TableHeadInterface) {
                $columns[] = $column->toArray();
            }
        }

        return $columns;
    }

    private function getRows(Table $table, Query $query): array
    {
        $rows = [];

        if ($connector = $table->getConnector()) {
            $iterator = $connector->getRow($query);

            foreach ($iterator as $row) {
                $rows[] = $this->getRow($table, $row);
            }
        }

        return $rows;
    }

    private function getRow(Table $table, array $row): array
    {
        $actions = [];
        $cells = [];

        foreach ($table->getColumns() as $column) {
            assert($column instanceof TableColumn);

            if ($column instanceof TableRowInterface) {
                $actions[] = $column->toArray($row);
            } else if ($column instanceof Column) {
                $cells[$column->getKey()] = $column->getRenderValue($row);
            }
        }

        $gridData = [
            'data' => $cells
        ];

        if ($actions) {
            $gridData['actions'] = $actions;
        }

        return $gridData;
    }
}