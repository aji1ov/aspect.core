<?php

namespace Aspect\Lib;

enum Context: string
{
    case WEB = 'web';
    case API = 'api';
    case CLI = 'cli';
    case CRON = 'cron';
    case TEST = 'test';
}
