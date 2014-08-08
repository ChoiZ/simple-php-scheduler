<?php

include 'function.php';
include 'rules.php';

$folder = $rules['music']['folder'];

$cr = "\n";
if (DIRECTORY_SEPARATOR == "\\") {
    $cr = "\r\n";
}

$tracks = read_folder($folder);

foreach($tracks as $track) {
    $path_track = pathinfo($track);
    if (in_array($path_track['extension'], $rules['music']['ext'])) {
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

do {

    $track = get_track($i,$bac,$playlist);

    if ($track) {
        $playlist[] = $track;
        $nb_pl++;
    }

    $i++;

} while($nb_pl < $rules['playlist']['size']);

$m3u_content = '#EXTM3U'.$cr;

foreach ($playlist as $item) {
    $m3u_content .= '#EXTINF:'.$item->duration.', '.$item->artist.' - '.$item->title.$cr;
    $m3u_content .= $item->filename.$cr;
}

if (file_put_contents($rules['playlist']['path'], $m3u_content) !== FALSE) {
    echo 'Playlist '.$rules['playlist']['path'].' saved.'.$cr;
}
