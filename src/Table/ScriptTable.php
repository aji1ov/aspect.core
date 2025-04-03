<?php

namespace Aspect\Lib\Table;

use Bitrix\Main\Entity;

class ScriptTable extends Entity\DataManager
{
    /**
     * @return string
     */
    public static function getTableName()
    {

        return 'aspect_script';
    }

    /**
     * @return Entity\IntegerField[]
     * @throws \Bitrix\Main\SystemException
     */
    public static function getMap()
    {
        return [
            new Entity\IntegerField('ID', [
                'primary' => true,
                'autocomplete' => true
            ]),
            new Entity\TextField('NAME', [
                'required' => true
            ]),
            new Entity\BooleanField('RUNNING', [
            ]),
            new Entity\BooleanField('CANCELLED', [
            ]),
            new Entity\TextField('OUTPUT', [
            ]),
        ];
    }
}