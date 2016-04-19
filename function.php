<?php
define('DEBUG', false);

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
