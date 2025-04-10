<?php

namespace Aspect\Lib\Table;

use Bitrix\Main\ORM\Fields;
use Bitrix\Main\ORM\Data\DataManager;

class QueueTable extends DataManager
{
    /**
     * @return string
     */
    public static function getTableName()
    {
        return 'aspect_queue';
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
            new Fields\TextField('SERIAL', [
                'required' => true
            ]),
            new Fields\TextField('SIGN', [
                'required' => true
            ]),
            new Fields\TextField('TAG', [
                'required' => true
            ]),
            new Fields\IntegerField('START_AT', [
                'required' => true
            ]),
            new Fields\BooleanField('BUSY'),

        ];
    }

}