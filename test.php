<?php
require_once "./test/empty_test/empty_test.php";
require_once "./test/function_parser_test/function_parser_test.php";
require_once "./test/inner_directory_test/inner_directory_test.php";
require_once "./test/one_file_test/one_file_test.php";
require_once "./test/spoiled_cache_test/spoiled_cache_test.php";

var_dump("empty_test");
empty_test();

var_dump("function_parser_test");
test_function_parser();

var_dump("inner_directory_test");
inner_directory_test();

var_dump("one_file_test");
one_file_test();

var_dump("spoiled_cache_test");
spoiled_cache_test();

