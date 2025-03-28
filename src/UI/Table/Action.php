<?php

namespace Aspect\Lib\UI\Table;

use Aspect\Lib\Ajax\Callback;
use Closure;

class Action extends TableColumn implements TableRowInterface
{
    private ?Closure $handler = null;

    public function render(Closure $handler): static
    {
        $this->handler = $handler;
        return $this;
    }

    public function toArray(array $row): array
    {
        $onClickHandler = null;

        if ($this->handler) {
            $handler = $this->handler;
            $handlerFunction = $handler($row);
            if ($handlerFunction instanceof Callback) {
               $onClickHandler = $handlerFunction->toJavaScript();
            }
        }

        $actionData = [
            'text' => $this->getTitle(),
        ];

        if ($onClickHandler) {
           $actionData['onclick'] = $onClickHandler;
        }

        return $actionData;
    }
}