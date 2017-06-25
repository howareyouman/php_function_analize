<?php

include_once __DIR__ . "/../../cache/Cache.php";

function empty_test() {
    $cache = new \Cache(__DIR__ . "/empty_dir");
    $function_cache = __DIR__ . DIRECTORY_SEPARATOR ."empty_dir"
        . DIRECTORY_SEPARATOR ."__cache__.json";
    $file_index_cache = __DIR__ . DIRECTORY_SEPARATOR . "empty_dir"
        . DIRECTORY_SEPARATOR . "__file_index__.json";
    $test_result = true;
    if (file_exists($function_cache) && file_exists($file_index_cache)) {
        $file_text = json_decode("[]");
        $test_result &= ($file_text == json_decode(file_get_contents($function_cache)));
        $test_result &= ($file_text == json_decode(file_get_contents($file_index_cache)));
        if ($test_result) {
            unlink($function_cache);
            unlink($file_index_cache);
        }
    }
    var_dump($test_result == true);
}
empty_test();