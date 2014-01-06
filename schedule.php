<?php

include 'function.php';

$rules = array(
    'artist_separation' => 2,
    'track_separation' => 6,
    'playlist_size' => 60
);

$folder = realpath(dirname(__FILE__))."/music";

$tracks = read_folder($folder);

foreach($tracks as $track) {
    list($artist, $title) = explode(' - ', $track);
    $artist_list[] = $artist;
    $track_list[] = $title;
    $song = new stdclass;
    $song->artist = $artist;
    $song->title = $title;
    $song->duration = rand(135,305);
    $songs[] = $song;
}

$bac = $songs;
shuffle($bac);

$max_artist = floor(count(array_unique($artist_list))/2);
$max_track = floor(count(array_unique($track_list))/2);

$playlist = array();
$i = 0;
$nb_pl = 0;

do {

    if ($max_artist < $rules['artist_separation']) {
        echo "WARNING: artist_separation is to high: ".$rules['artist_separation']." use a value between 1 and ".$max_artist."\n";
        exit;
    }

    if ($max_track < $rules['track_separation']) {
        echo "WARNING: track_separation is to high: ".$rules['track_separation']." use a value between 1 and ".$max_track."\n";
        exit;
    }

    $track = get_track($i,$bac,$playlist);

    if ($track) {
        $playlist[] = $track;
        $nb_pl++;
    }

    $i++;

} while($nb_pl < $rules['playlist_size']);

foreach ($playlist as $item) {
    echo $item->artist." - ".$item->title."\n";
}