<?php

namespace Aspect\Lib\Render;

use Aspect\Lib\Support\Interfaces\ViewInterface;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\Page\AssetShowTargetType;

class ControllerView implements ViewInterface
{

    private string $componentName;
    private string $template;
    private string $view = 'template';
    private array $componentParams = [];


    public function __construct(string $componentName, string $template = '.default') {
        $this->componentName = $componentName;
        $this->template = $template;
    }

    public function render(): string
    {
        $componentClass = \CBitrixComponent::includeComponentClass($this->componentName);
        $component = new $componentClass();
        assert($component instanceof \CBitrixComponent);

        $component->initComponent($this->componentName, $this->template);
        $component->arParams = $this->componentParams;

        ob_start();
        $component->includeComponentTemplate($this->view);

        return ob_get_clean();
    }

    public function renderAjax(): array
    {
        return [
            'HTML' => $this->render(),
            'SCRIPT' => Asset::getInstance()->getJs(AssetShowTargetType::TEMPLATE_PAGE),
            'CSS' => Asset::getInstance()->getCss(AssetShowTargetType::TEMPLATE_PAGE)
        ];
    }

    public function setTemplate(string $template): static
    {
        $this->template = $template;
        return $this;
    }

    public function setComponentParams(array $componentParams): static
    {
        $this->componentParams = $componentParams;
        return $this;
    }

    public function setView(string $view): static
    {
        $this->view = $view;
        return $this;
    }

    public static function get(string $component): static
    {
        return new static($component);
    }
}