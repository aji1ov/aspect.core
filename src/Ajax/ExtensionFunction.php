<?php

namespace Aspect\Lib\Ajax;


use Bitrix\Main\ArgumentException;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Web\Json;

class ExtensionFunction extends DependedExpression
{

    private string $module;
    private string $function;
    private array $argumentList = [];

    /**
     * @throws LoaderException
     */
    public function __construct(string $module, string $function, mixed ...$arguments)
    {
        list($m, ) = explode(".", $module);
        $this->module = $m;
        $this->function = $function;
        $this->argumentList = $this->makeJsArgumentList(...$arguments);
        $this->withDependency($module);
    }

    private function makeJsArgumentList(mixed ...$arguments): array
    {
        return array_map(
            /**
             * @throws ArgumentException
             */
            fn ($argument) => Json::encode($argument),
            $arguments
        );
    }

    /**
     * @throws LoaderException
     */
    public static function invoke(string $module, string $function, ...$arguments): static
    {
        return new static($module, $function, ...$arguments);
    }

    function toJavaScript(): string
    {
        return 'BX.'.ucfirst($this->module).'.'.$this->function.'('.implode(", ", $this->argumentList).')';
    }
}