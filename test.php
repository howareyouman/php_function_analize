<?php
require_once "./test/empty_test/empty_test.php";
require_once "./test/function_parser_test/function_parser_test.php";
require_once "./test/inner_directory_test/inner_directory_test.php";
require_once "./test/one_file_test/one_file_test.php";
require_once "./test/spoiled_cache_test/spoiled_cache_test.php";
require_once "./test/load_cache_test/load_cache_test.php";

test();

function test()
{
    print_result(empty_test(), "empty_test");
    print_result(test_function_parser(), "function_parser_test");
    print_result(inner_directory_test(), "inner_directory_test");
    print_result(one_file_test(), "one_file_test");
    print_result(spoiled_cache_test(), "spoiled_cache_test");
    print_result(load_cache_test(), "load_cache_test");
}


function print_result($result, $test_fail = "")
{
    if ($result != true) {
        var_dump($test_fail . " fails!");
    } else {
        var_dump($test_fail . " - OK!");
    }
}


