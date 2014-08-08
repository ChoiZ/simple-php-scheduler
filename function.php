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

function get_track($i,$bac,$playlist) {

    global $rules;
    $j = $i % count($bac);
    $artist_last_diff = array_slice($playlist, -$rules['separation']['artist'], $rules['separation']['artist']);
    $track_last_diff = array_slice($playlist, -$rules['separation']['track'], $rules['separation']['track']);

    if (isset($bac[$j])) {
        if (in_object($bac[$j]->artist, $artist_last_diff,'artist')) {
            if(DEBUG) {
                echo "WARNING: artist already diff\n";
            }
        } else if (in_object($bac[$j]->title, $track_last_diff,'title')) {
            if(DEBUG) {
                echo "WARNING: track already diff\n";
            }
        } else {
            if(DEBUG) {
                echo "ok $j\n";
            }
            return $bac[$j];
        }
    }

    return false;

}

function in_object($s,$o,$type) {
    foreach($o as $el) {
        foreach($el as $k => $v) {
            if($type == $k) {
                if($s == $v) {
                    return true;
                }
            }
        }
    }
    return false;
}
