<?php

include 'function.php';

$rules = array(
    'artist_separation' => 4,
    'track_separation' => 6,
    'playlist_size' => 60
);

$folder = realpath(dirname(__FILE__))."/music";

$tracks = read_folder($folder);

foreach($tracks as $track) {
    list($artist, $title) = explode(' - ', $track);
    $song = new stdclass;
    $song->artist = $artist;
    $song->title = $title;
    $song->duration = rand(135,305);
    $songs[] = $song;
}

$playlist = array();
$i = 0;

$bac = $songs;
shuffle($bac);

do {

    $track = get_track($i,$bac,$playlist);

    if($track) {
        $playlist[] = $track;
    }

    $nb_pl = count($playlist);
    $i++;

} while($nb_pl < $rules['playlist_size']);

foreach ($playlist as $item) {
    echo $item->artist." - ".$item->title."\n";
}
