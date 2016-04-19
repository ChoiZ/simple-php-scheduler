<?php

include_once 'function.php';
include_once 'rules.php';

$cr = "\n";
if (DIRECTORY_SEPARATOR == "\\") {
    $cr = "\r\n";
}

if (!empty($argv[1])) {
    $rules_file = $argv[1];
    if (file_exists($rules_file)) {
        include_once($rules_file);
    }
}

$artist_list = array();
$track_list = array();
$bac = array();
$playlist = array();
$playlist['artist'] = array();
$playlist['title'] = array();
$playlist['filename'] = array();
$playlist['duration'] = array();

$folder = $rules['music']['folder'];

$tracks = read_folder($folder);

foreach($tracks as $track) {
    $path_track = pathinfo($track);

    if (in_array($path_track['extension'], $rules['music']['ext'])) {
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
$max_track = floor(count(array_unique($track_list))/2);

echo "max artist : ".$max_artist."\n";
echo "max track : ".$max_track."\n";
echo "max bac : ".count($bac)."\n";

$error = array();

if (empty($rules['separation']['artist'])) {
    $error[] = 'WARNING: artist_separation must be set in rules.php file.';
}

if (empty($rules['separation']['track'])) {
    $error[] = 'WARNING: track_separation must be set in rules.php file.';
}

if (empty($rules['playlist']['path'])) {
    $error[] = 'WARNING: playlist_path must be set in rules.php file.';
}

if (empty($rules['playlist']['size'])) {
    $error[] = 'WARNING: playlist_size must be set in rules.php file.';
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

} while(count($playlist['artist']) < $rules['playlist']['size']);

$m3u_content = '#EXTM3U'.$cr;

foreach ($playlist['filename'] as $item) {
    //$m3u_content .= '#EXTINF:'.$item->duration.', '.$item->artist.' - '.$item->title.$cr;
    $m3u_content .= $item.$cr;
}

if (file_put_contents($rules['playlist']['path'], $m3u_content) !== FALSE) {
    echo 'Playlist '.$rules['playlist']['path'].' saved.'.$cr;
}
