<?php
define('DEBUG', false);
$cr = "\n";

if (DIRECTORY_SEPARATOR == "\\") {
    $cr = "\r\n";
}

include_once 'config.php';

if (!empty($argv[1])) {
    $rules_file = $argv[1];
    if (file_exists($rules_file)) {
        include_once($rules_file);
    } else {
        include_once 'rules/separation.php';
    }
} else {
    include_once 'rules/separation.php';
}

$artist_list = array();
$track_list = array();
$bac = array();
$playlist = array();
$playlist['artist'] = array();
$playlist['title'] = array();
$playlist['filename'] = array();
$playlist['duration'] = array();

$folder = $config['music']['folder'];

$tracks = read_folder($folder);

foreach($tracks as $track) {
    $path_track = pathinfo($track);

    if (in_array($path_track['extension'], $config['music']['ext'])) {
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

$max_artist = floor(count(array_unique($artist_list))/2);
$max_track = floor(count(array_unique($track_list)));

echo "max artist : ".$max_artist."\n";
echo "max track : ".$max_track."\n";
echo "max bac : ".count($bac)."\n";

$error = array();

if (empty($config['playlist']['path'])) {
    $error[] = 'WARNING: playlist_path must be set in config.php file.';
}

if (empty($config['playlist']['size'])) {
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

    $track = get_track($i, $bac, $playlist);

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

} while(count($playlist['artist']) < $config['playlist']['size']);

$m3u_content = '#EXTM3U'.$cr;

foreach ($playlist['filename'] as $item) {
    //$m3u_content .= '#EXTINF:'.$item->duration.', '.$item->artist.' - '.$item->title.$cr;
    $m3u_content .= $item.$cr;
}

if (file_put_contents($config['playlist']['path'], $m3u_content) !== FALSE) {
    echo 'Playlist '.$config['playlist']['path'].' saved.'.$cr;
}

function read_folder($folder) {
    if ($handle = opendir($folder)) {
        $out = array();
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                $out[] = $entry;
            }
        }
        closedir($handle);
        return $out;
    }
    return false;
}

function get_track($i, $bac, $playlist) {

    global $rules;

    $j = $i % count($bac);
    $artist_last_diff = array_slice($playlist['artist'], -$rules['separation']['artist'], $rules['separation']['artist']);
    $track_last_diff = array_slice($playlist['title'], -$rules['separation']['track'], $rules['separation']['track']);

    if (isset($bac[$j])) {
        if (in_array(strtolower($bac[$j]['artist']), $artist_last_diff)) {
            if (DEBUG) {
                echo "WARNING: artist ".strtolower($bac[$j]['artist'])." already diff\n";
            }
            return false;
        }
        if (in_array(strtolower($bac[$j]['title']), $track_last_diff)) {
            if (DEBUG) {
                echo "WARNING: track ".strtolower($bac[$j]['title'])." already diff\n";
            }
            return false;
        }
        if (DEBUG) {
            echo "Track Add ".$bac[$j]['title']."\n";
            echo "Artist Add ".$bac[$j]['artist']."\n";
        }
        return $bac[$j];
    }

    return false;

}
