<?php

namespace Aspect\Lib\Repository;

use Bitrix\Main\Entity;

class ScheduleTable extends Entity\DataManager
{
    /**
     * @return string
     */
    public static function getTableName()
    {
        return 'aspect_schedule';
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
            new Entity\TextField('SIGN', [
                'required' => true
            ]),
            new Entity\IntegerField('CHECK_TIME', [
                'required' => true
            ]),

        ];
    }

}