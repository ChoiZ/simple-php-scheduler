<?php

include 'function.php';
include 'rules.php';

$folder = realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR.'music'.DIRECTORY_SEPARATOR;

$cr = "\n";
if (DIRECTORY_SEPARATOR == "\\") {
    $cr = "\r\n";
}

$tracks = read_folder($folder);

foreach($tracks as $track) {
    $path_track = pathinfo($track);
    if (in_array($path_track['extension'], $rules['allowed_extensions'])) {
        list($artist, $title) = explode(' - ', $path_track['filename']);
        $artist_list[] = $artist;
        $track_list[] = $title;
        $song = new stdclass;
        $song->artist = $artist;
        $song->title = $title;
        $song->filename = $folder.$track;
        $song->duration = rand(135,305);
        $songs[] = $song;
    }
}

$bac = $songs;
shuffle($bac);

$max_artist = floor(count(array_unique($artist_list))/2);
$max_track = floor(count(array_unique($track_list))/2);

$playlist = array();
$i = 0;
$nb_pl = 0;


$error = array();

if (empty($rules['artist_separation'])) {
    $error[] = 'WARNING: artist_separation must be set in rules.php file.';
}

if (empty($rules['track_separation'])) {
    $error[] = 'WARNING: track_separation must be set in rules.php file.';
}

if (empty($rules['playlist_name'])) {
    $error[] = 'WARNING: playlist_name must be set in rules.php file.';
}

if (empty($rules['playlist_size'])) {
    $error[] = 'WARNING: playlist_size must be set in rules.php file.';
}

if ($max_artist < $rules['artist_separation']) {
    $error[] = 'WARNING: artist_separation is too high: '.$rules['artist_separation'].' use a value between 1 and '.$max_artist;
}

if ($max_track < $rules['track_separation']) {
    $error[] = 'WARNING: track_separation is too high: '.$rules['track_separation'].' use a value between 1 and '.$max_track;
}

if (count($error)>0) {
    foreach($error as $msg) {
        echo $msg.$cr;
    }
    exit();
}

do {

    $track = get_track($i,$bac,$playlist);

    if ($track) {
        $playlist[] = $track;
        $nb_pl++;
    }

    $i++;

} while($nb_pl < $rules['playlist_size']);

$m3u_content = '#EXTM3U'.$cr;

foreach ($playlist as $item) {
    $m3u_content .= '#EXTINF:'.$item->duration.', '.$item->artist.' - '.$item->title.$cr;
    $m3u_content .= $item->filename.$cr;
}

if (file_put_contents($rules['playlist_name'], $m3u_content) !== FALSE) {
    echo 'Playlist '.$rules['playlist_name'].' saved.';
}
