<?php

namespace Aspect\Lib\Service\Admin;

use Aspect\Lib\Struct\ServiceLocator;

class AdminMenuService
{
    use ServiceLocator;

    public function enrichGlobalMenu(array $global)
    {
        $global['global_menu_aspect'] = array(
            'menu_id' => 'aspect',
            'text' => 'Aspect',
            'title' => 'Aspect',
            'url' => 'index.php?lang=ru',
            'sort' => 50,
            'items_id' => 'global_menu_aspect',
            'help_section' => 'global_menu_aspect',
            'page_icon' => 'aspect',
            'items' => $this->getSectionItems()
        );

        return $global;
    }

    private function getSectionItems(): array
    {
        return [
            $this->getCronItems(),
            $this->getCommandItems(),
            $this->getInfoItems()
        ];
    }

    private function getInfoItems(): array
    {
        return [
            "parent_menu" => 'global_menu_aspect',
            "text" => "Информация для разработчика",
            "title" => "title",
            "icon" => "fileman_sticker_icon",
            "items" => [
                [
                    "text" => 'Планировщик задач',
                    "title" => "title",
                    "url" => "/bitrix/admin/aspect_info_cron.php",
                ]
            ]
        ];
    }

    private function getCronItems(): array
    {
        return [
            "parent_menu" => "global_menu_aspect",
            "text" => 'Планировщик задач',
            "title" => "title",
            "icon" => "fileman_menu_icon",
            "url" => "/bitrix/admin/aspect_queue_list.php",
        ];
    }

    private function getCommandItems(): array
    {
        return [
            "parent_menu" => "global_menu_aspect",
            "text" => 'Команды',
            "title" => "title",
            "icon" => "fileman_menu_icon",
            "items" => [
                [
                    "text" => 'PHP скрипт',
                    "title" => "title",
                    "url" => "/bitrix/admin/aspect_script_list.php",
                    "more_url" => [
                        "/bitrix/admin/aspect_script_view.php"
                    ]
                ]
            ]
        ];
    }
}