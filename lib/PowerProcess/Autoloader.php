<?php

class BauerBox_PowerProcess_Autoloader
{
    public static $classBase = 'BauerBox\\PowerProcess\\';

    public static function register()
    {
        spl_autoload_register(array(new self, 'autoload'));
    }

    /**
     * Handles autoloading of classes.
     *
     * @param string $class A class name.
     */
    public static function autoload($class)
    {
        if (0 !== strpos($class, static::$classBase)) {
            return;
        }

        $class = str_replace(array(static::$classBase, '\\'), array('', '/'), $class);

        if (is_file($file = dirname(__FILE__).'/'.str_replace(array('_', "\0"), array('/', ''), $class).'.php')) {
            require $file;
        }
    }
}
