<?php

namespace Aspect\Lib\Preset\Event;

use Aspect\Lib\Blueprint\Event\ArrayEvent;
use Aspect\Lib\Event\EventPackage;
use Aspect\Lib\Event\ListEventSource;

class Main extends EventPackage
{
    #[ArrayEvent(takeTo: ['global', 'module'])]
    public function OnBuildGlobalMenu(ListEventSource $source)
    {
        $global = $source->getValue('global');

        $global['global_menu_aspect'] = array(
            'menu_id' => 'aspect',
            'text' => 'Aspect',
            'title' => 'Aspect',
            'url' => 'index.php?lang=ru',
            'sort' => 50,
            'items_id' => 'global_menu_aspect',
            'help_section' => 'global_menu_aspect',
            'page_icon' => 'aspect',
            'items' => [
                [
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
                ]
            ]
        );
        $source->setOutput('global', $global);
    }
}