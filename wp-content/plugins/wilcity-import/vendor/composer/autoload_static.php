<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit3172ea55e0141fe7f6facbb1eba95a6f
{
    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'WILCITY_IMPORT\\' => 15,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'WILCITY_IMPORT\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit3172ea55e0141fe7f6facbb1eba95a6f::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit3172ea55e0141fe7f6facbb1eba95a6f::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
