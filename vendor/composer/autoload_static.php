<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit02c91c088b27196452adac789415051f
{
    public static $prefixLengthsPsr4 = array (
        't' => 
        array (
            'tpmanc\\imagick\\' => 15,
        ),
        'D' => 
        array (
            'Data\\' => 5,
            'DLTools\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'tpmanc\\imagick\\' => 
        array (
            0 => __DIR__ . '/..' . '/tpmanc/yii2-imagick',
        ),
        'Data\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'DLTools\\' => 
        array (
            0 => __DIR__ . '/..' . '/dlunamontilla/dltools/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'DLTools\\Controllers\\DLCalendar' => __DIR__ . '/..' . '/dlunamontilla/dltools/src/Controllers/DLCalendar.php',
        'DLTools\\Controllers\\DLConfig' => __DIR__ . '/..' . '/dlunamontilla/dltools/src/Controllers/DLConfig.php',
        'DLTools\\Controllers\\DLHost' => __DIR__ . '/..' . '/dlunamontilla/dltools/src/Controllers/DLHost.php',
        'DLTools\\Controllers\\DLProtocol' => __DIR__ . '/..' . '/dlunamontilla/dltools/src/Controllers/DLProtocol.php',
        'DLTools\\Controllers\\DLRequest' => __DIR__ . '/..' . '/dlunamontilla/dltools/src/Controllers/DLRequest.php',
        'DLTools\\Controllers\\DLSubir' => __DIR__ . '/..' . '/dlunamontilla/dltools/src/Controllers/DLSubir.php',
        'DLTools\\Models\\Authenticate' => __DIR__ . '/..' . '/dlunamontilla/dltools/src/Models/Authenticate.php',
        'DLTools\\Models\\DLUser' => __DIR__ . '/..' . '/dlunamontilla/dltools/src/Models/DLUser.php',
        'DLTools\\Models\\Database' => __DIR__ . '/..' . '/dlunamontilla/dltools/src/Models/Database.php',
        'Data\\ProcessCSV' => __DIR__ . '/../..' . '/src/ProcessCSV.php',
        'tpmanc\\imagick\\Imagick' => __DIR__ . '/..' . '/tpmanc/yii2-imagick/Imagick.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit02c91c088b27196452adac789415051f::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit02c91c088b27196452adac789415051f::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit02c91c088b27196452adac789415051f::$classMap;

        }, null, ClassLoader::class);
    }
}
