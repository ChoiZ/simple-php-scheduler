<?php
/**
 * Checks if $num is a Prime Number
 * @param int $num
 * @return boolean
 */
function isPrime($num) {
    if ($num == 1) {
        return false;
    }

    if ($num == 2) {
        return true;
    }

    if ($num % 2 == 0) {
        return false;
    }

    for ($i = 3; $i <= ceil(sqrt($num)); $i = $i + 2) {
        if ($num % $i == 0) {
            return false;
        }
    }

    return true;
}

for ($i=0; $i<121; $i++) {
    if(isPrime($i)) {
        echo $i."\n";
    }
}
