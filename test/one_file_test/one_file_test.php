<?php
include_once __DIR__ . "/../../cache/Cache.php";

function test_one_function() {
    $cache = new \Cache(__DIR__ . DIRECTORY_SEPARATOR ."test_directory");
    var_dump($cache->check_function("one_func 3") == true);
}

test_one_function();