<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit123f1c527290d1202b7c99a4c7f21510
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Service\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Service\\' => 
        array (
            0 => __DIR__ . '/../..' . '/Service',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit123f1c527290d1202b7c99a4c7f21510::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit123f1c527290d1202b7c99a4c7f21510::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
