<?php
require_once("./Cache.php");

main($argc, $argv);

function main($argc, $argv)
{
    if ($argc != 3) {
        echo "Incorrect number of params!";
        return;
    }

    $directory = $argv[1];
    $file = $argv[2];

    try {
        $cache = new Cache($directory);

    } catch (PathException $exception) {

    }


}



