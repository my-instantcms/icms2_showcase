<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit6ba9a22d3c54e25c010286770aa517a7
{
    public static $prefixLengthsPsr4 = array (
        'D' => 
        array (
            'DiDom\\' => 6,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'DiDom\\' => 
        array (
            0 => __DIR__ . '/..' . '/imangazaliev/didom/src/DiDom',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit6ba9a22d3c54e25c010286770aa517a7::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit6ba9a22d3c54e25c010286770aa517a7::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
