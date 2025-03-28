<?php

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$APPLICATION->SetTitle("Список PHP-скриптов");

$APPLICATION->IncludeComponent("aspect:script.view", ".default", ['NAME' => $_REQUEST['script']]);