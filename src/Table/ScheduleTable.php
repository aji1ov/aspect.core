<?php

namespace Aspect\Lib\Table;

use Bitrix\Main\ORM\Fields;
use Bitrix\Main\ORM\Data\DataManager;

class ScheduleTable extends DataManager
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
            new Fields\IntegerField('ID', [
                'primary' => true,
                'autocomplete' => true
            ]),
            new Fields\TextField('SIGN', [
                'required' => true
            ]),
            new Fields\IntegerField('CHECK_TIME', [
                'required' => true
            ]),

        ];
    }

}