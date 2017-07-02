<?php
include_once __DIR__ . "/../../cache/Cache.php";

function spoiled_cache_test()
{
    $directory = __DIR__ . DIRECTORY_SEPARATOR . "test_directory";
    $cache = new \Cache($directory);
    $test_result = $cache->check_function("abc 0");
    $result = file_put_contents($directory . DIRECTORY_SEPARATOR . "file.php", "abc();", FILE_APPEND);
    if ($result !== false) {
        $test_result |= $cache->check_function("abc 0");
        var_dump($test_result == true);
        if ($test_result) {
            unlink($directory . DIRECTORY_SEPARATOR . "__cache__.json");
            unlink($directory . DIRECTORY_SEPARATOR . "__file_index__.json");
        }
    }
}
