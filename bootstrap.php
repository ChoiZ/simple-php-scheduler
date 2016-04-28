<?php

if (!file_exists(__DIR__.'/config.php')) {
    exit('Missing config file!');
}

require_once __DIR__.'/config.php';

$config_dist = file_get_contents(__DIR__.'/config.php.dist');
preg_match("/CONFIG', (.*)\)/", $config_dist, $version);

if (CONFIG != $version[1]) {
    exit('Config is not up to date!');
}

define('DEBUG', false);

ini_set('default_charset', ENCODING);
ini_set('php.input_encoding', ENCODING);
ini_set('php.internal_encoding', ENCODING);
ini_set('php.output_encoding', ENCODING);
date_default_timezone_set(TIMEZONE);

function loader($class)
{
    $class = str_replace('\\', '/', $class);
    $file = __DIR__.'/'.strtolower($class).'.php';
    try {
        if (file_exists($file)) {
            require_once $file;
        }
    } catch (Exception $e) {
        echo 'Exception : ',  $e->getMessage(), "\n";
    }
}
spl_autoload_register('loader');

$config = new Engine\Config($music_ext, $music_path, $playlist_path, $playlist_size, $stations);

$schedule = new Engine\Schedule($config);
