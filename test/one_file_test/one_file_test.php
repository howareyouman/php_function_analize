<?php
include_once __DIR__ . "/../../cache/Cache.php";

function one_file_test()
{
    $directory = __DIR__ . DIRECTORY_SEPARATOR . "test_directory";
    $cache = new \Cache($directory);
    $test_result = $cache->check_function("one_func 3");
    var_dump($test_result == true);
    if ($test_result) {
        unlink($directory . DIRECTORY_SEPARATOR . "__cache__.json");
        unlink($directory . DIRECTORY_SEPARATOR . "__file_index__.json");
    }
}
