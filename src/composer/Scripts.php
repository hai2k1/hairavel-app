<?php

namespace Hairavel\Composer;

use Composer\Script\Event;
use Illuminate\Foundation\Application;

class Scripts
{
    public static function postInstall(Event $event)
    {
        static::clearCompiled();
    }

    public static function postUpdate(Event $event)
    {
        static::clearCompiled();
    }

    public static function postAutoloadDump(Event $event)
    {
        static::clearCompiled();
    }

    public static function clearCompiled()
    {
        $laravel = new Application(getcwd());
        if (is_file($servicesPath = $laravel->bootstrapPath('cache/hairavel.php'))) {
            @unlink($servicesPath);
        }

    }
}
