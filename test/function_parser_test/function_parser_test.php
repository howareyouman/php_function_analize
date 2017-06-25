<?php
include_once dirname(__FILE__) . '/../../function_parser/FunctionParser.php';

test_function_parser();

function test_function_parser() {
    $function_parser = new \FunctionParser();
    simple_function_annotation_test($function_parser);
    simple_functions_call($function_parser);
}

function simple_function_annotation_test($function_parser) {
    $correct_array = ['test 2', 'test_asd 0', 'test_1 3'];
    var_dump($correct_array == $function_parser->parse_files_functions("simple_functions_annotations.php"));
}

function simple_functions_call($function_parser) {
    $correct_array = ['test 2', 'my_1_function 1'];
    var_dump($correct_array == $function_parser->parse_files_usage_functions("simple_function_calls.php"));
}

