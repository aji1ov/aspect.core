<?php

namespace Aspect\Lib;

enum Gateway
{
    private const WEB_SAPI_NAMES = ['fpm-fcgi', 'cgi-fcgi', 'apache', 'apache2handler'];
    private const CLI_SAPI_NAMES = ['cli', 'cli-server'];

    case CLI;
    case WEB;
    case UNKNOWN;

    static function detect(): Gateway
    {
        $sapi = php_sapi_name();
        if(in_array($sapi, Gateway::WEB_SAPI_NAMES)) {
            return Gateway::WEB;
        } else if(in_array($sapi, Gateway::CLI_SAPI_NAMES)) {
            return Gateway::CLI;
        }

        return Gateway::UNKNOWN;
    }
}
