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

$cr = "\n";

if (DIRECTORY_SEPARATOR == "\\") {
    $cr = "\r\n";
}

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

$config = new Engine\Config($music_ext, $music_path, $playlist_path, $playlist_size);

foreach ($stations as $station_name => $rules) {
    $config->addStation(new Engine\Station($station_name, $rules));
}

$schedule = new Engine\Schedule();

//
//
// TODO: Rewrite after this line ;)
//
//
$stations = $config->getStations();
$nb_station = count($stations);

if ($nb_station > 0) {

    foreach ($stations as $station) {

        $station_name = $station->getName();
        $rules = $station->getRules();
        echo "Station : ";
        echo $station_name."\n";
        echo "Rules : \n";
        print_r($rules);

        $toolong = false;
        $artist_list = array();
        $track_list = array();
        $bac = array();
        $playlist = array();
        $playlist['artist'] = array();
        $playlist['title'] = array();
        $playlist['filename'] = array();
        $playlist['duration'] = array();
        $folder = $config->getMusicFolder().$station_name.'/';

        $tracks = $schedule->readFolder($folder);

        foreach($tracks as $track) {
            $path_track = pathinfo($track);

            if (in_array($path_track['extension'], $config->getMusicExt())) {
                list($artist, $title) = explode(' - ', $path_track['filename']);
                $artist_list[] = strtolower($artist);
                $track_list[] = strtolower($title);
                $song = array();
                $song['artist'] = $artist;
                $song['title'] = $title;
                $song['filename'] = $folder.$track;
                $song['duration'] = rand(135,305);
                $bac[] = $song;
            }
        }

        shuffle($bac);

        $max_artist = floor(count(array_unique($artist_list)));
        $max_track = floor(count(array_unique($track_list)));

        echo "max artist : ".$max_artist."\n";
        echo "max track : ".$max_track."\n";
        echo "max bac : ".count($bac)."\n";

        $error = array();

        if (empty($config->getPlaylistPath())) {
            $error[] = 'WARNING: playlist_path must be set in config.php file.';
        }

        if (empty($config->getPlaylistSize())) {
            $error[] = 'WARNING: playlist_size must be set in config.php file.';
        }

        if (empty($rules['separation']['artist'])) {
            $error[] = 'WARNING: artist_separation must be set in separation.php file.';
        }

        if (empty($rules['separation']['track'])) {
            $error[] = 'WARNING: track_separation must be set in separation.php file.';
        }

        if ($max_artist < $rules['separation']['artist']) {
            $error[] = 'WARNING: artist_separation is too high: '.$rules['separation']['artist'].' use a value between 1 and '.$max_artist;
        }

        if ($max_track < $rules['separation']['track']) {
            $error[] = 'WARNING: track_separation is too high: '.$rules['separation']['track'].' use a value between 1 and '.$max_track;
        }

        if (count($error)>0) {
            foreach($error as $msg) {
                echo $msg.$cr;
            }
            exit();
        }

        $i=0;

        do {

            $track = $schedule->getTrack($i, $bac, $playlist, $rules);

            if ($track !== false) {
                $playlist['artist'][] = strtolower($track['artist']);
                $playlist['duration'][] = $track['duration'];
                $playlist['filename'][] = $track['filename'];
                $playlist['title'][] = strtolower($track['title']);
            }

            $i++;
            if ($i % $max_track == 2) {
                shuffle($bac);
            }

        } while(count($playlist['artist']) < $config->getPlaylistSize());

        $m3u_content = '#EXTM3U'.$cr;

        foreach ($playlist['filename'] as $item) {
            //$m3u_content .= '#EXTINF:'.$item->duration.', '.$item->artist.' - '.$item->title.$cr;
            $m3u_content .= $item.$cr;
        }

        if (file_put_contents($config->getPlaylistPath().$station_name.'.m3u', $m3u_content) !== FALSE) {
            echo 'Playlist '.$config->getPlaylistPath().$station_name.'.m3u saved.'.$cr;
        }
    }
}
