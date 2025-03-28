<?php

namespace Aspect\Lib\Repository;

use Bitrix\Main\Entity;

class QueueTable extends Entity\DataManager
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
            new Entity\IntegerField('ID', [
                'primary' => true,
                'autocomplete' => true
            ]),
            new Entity\TextField('SERIAL', [
                'required' => true
            ]),
            new Entity\TextField('SIGN', [
                'required' => true
            ]),
            new Entity\TextField('TAG', [
                'required' => true
            ]),
            new Entity\IntegerField('START_AT', [
                'required' => true
            ]),
            new Entity\BooleanField('BUSY'),

        ];
    }

}