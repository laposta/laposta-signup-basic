<?php

namespace Laposta\SignupBasic;

use Laposta\SignupBasic\Container\Container;

class Bootstrap
{
    /**
     * Prevent duplicate initialization if the entry file is loaded more than once.
     *
     * @var bool
     */
    protected static $booted = false;

    public static function run()
    {
        if (self::$booted) {
            return;
        }

        self::$booted = true;

        $container = new Container();
        $container->getPlugin();
    }
}
