<?php

namespace Aspect\Lib\Table;

use Bitrix\Main\ORM\Fields;
use Bitrix\Main\ORM\Data\DataManager;

class JobLogTable extends DataManager
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
            new Fields\IntegerField('ID', [
                'primary' => true,
                'autocomplete' => true
            ]),
            new Fields\TextField('NAME', [
                'required' => true
            ]),
            new Fields\BooleanField('SUCCESS', [

            ]),
            new Fields\IntegerField('STARTED_AT', [
                'required' => true
            ]),
            new Fields\IntegerField('DURATION', [
                'required' => true
            ]),
            (new Fields\ArrayField('ERRORS', [
            ]))->configureSerializationPhp(),

            (new Fields\ArrayField('WARNINGS', [
            ]))->configureSerializationPhp(),
        ];
    }

}