<?php

namespace Aspect\Lib\Repository;

use Bitrix\Main\Entity;
use Bitrix\Main\ORM\Fields\ArrayField;

class JobLogTable extends Entity\DataManager
{
    /**
     * @return string
     */
    public static function getTableName()
    {
        return 'aspect_job_log';
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
            new Entity\BooleanField('SUCCESS', [

            ]),
            new Entity\IntegerField('STARTED_AT', [
                'required' => true
            ]),
            new Entity\IntegerField('DURATION', [
                'required' => true
            ]),
            (new ArrayField('ERRORS', [
            ]))->configureSerializationPhp(),
            (new ArrayField('WARNINGS', [
            ]))->configureSerializationPhp(),
        ];
    }

}