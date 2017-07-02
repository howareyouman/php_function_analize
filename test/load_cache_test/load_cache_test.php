<?php
include_once __DIR__ . "/../../cache/Cache.php";

function load_cache_test() {
    $directory = __DIR__ . DIRECTORY_SEPARATOR . "test_directory";
    new \Cache($directory);
    $cache = new \Cache($directory);
    $test_result = $cache->check_function("one_func 3");
    if ($test_result) {
        unlink($directory . DIRECTORY_SEPARATOR . "__cache__.json");
        unlink($directory . DIRECTORY_SEPARATOR . "__file_index__.json");
    }
    return $test_result;
}