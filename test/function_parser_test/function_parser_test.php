<?php
include_once dirname(__FILE__) . '/../../function_parser/FunctionParser.php';

test_function_parser();

function test_function_parser() {
    $function_parser = new \FunctionParser();
    one_function_test($function_parser);
}

function one_function_test($function_parser) {
    $correct_array = ['test 2', 'test_asd 0', 'test_1 3'];
    var_dump($correct_array == $function_parser->parse_files_functions("simple_functions.php"));
}

