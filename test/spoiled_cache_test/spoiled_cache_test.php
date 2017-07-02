<?php
include_once __DIR__ . "/../../cache/Cache.php";

function spoiled_cache_test()
{
    $directory = __DIR__ . DIRECTORY_SEPARATOR . "test_directory";
    $cache = new \Cache($directory);
    $test_result = $cache->check_function("abc 0");
    $test_filename = $directory . DIRECTORY_SEPARATOR . "file.php";
    $result = file_put_contents($test_filename, "abc();", FILE_APPEND);
    if ($result !== false) {
        $test_result |= $cache->check_function("abc 0");
        if ($test_result) {
            unlink($directory . DIRECTORY_SEPARATOR . "__cache__.json");
            unlink($directory . DIRECTORY_SEPARATOR . "__file_index__.json");
            $lines = file($test_filename);
            $last = sizeof($lines) - 1 ;
            unset($lines[$last]);

            $fp = fopen($test_filename, 'w');
            fwrite($fp, implode('', $lines));
            fclose($fp);
        }
    }
    return $test_result;
}


