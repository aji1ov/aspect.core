<?php

namespace Aspect\Lib\Ajax;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Web\Json;

class ComponentAction extends DependedExpression
{
    private string $componentName;
    private string $routeType;
    private string $action;
    private array $parameters = [];
    private ?Callback $callback;

    public function __construct(string $componentName, string $routeType, string $action, array $parameters = [], ?Callback $callback = null)
    {
        $this->componentName = $componentName;
        $this->routeType = $routeType;
        $this->action = $action;
        $this->parameters = $parameters;
        $this->callback = $callback;

        $this->withDependency('aspect.route');
    }

    public static function call(string $componentName, string $routeType, string $action, array $parameters = [], ?Callback $callback = null): static
    {
        return new static($componentName, $routeType, $action, $parameters, $callback);
    }

    /**
     * @throws ArgumentException
     */
    function toJavaScript(): string
    {
        $inline = "BX.Aspect.route()".
            ".component('".$this->componentName."')"
            .".type('".$this->routeType."')"
            .".action('".$this->action."')"
            .".request(".Json::encode($this->parameters).")";

        if($this->callback) {
            $inline .= ".then(() => ".$this->callback->toJavaScript().")";
        }

        return $inline;
    }
}