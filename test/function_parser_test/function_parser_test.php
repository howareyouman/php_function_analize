<?php
include_once dirname(__FILE__) . '/../../function_parser/FunctionParser.php';

function test_function_parser() {
    $function_parser = new \FunctionParser();
    simple_function_annotation_test($function_parser);
    simple_functions_call($function_parser);
}

function simple_function_annotation_test($function_parser) {
    $file_info = FunctionParser::get_file_info(__DIR__ . "/simple_functions_annotations.php");
    $correct_array = ['test 2' => $file_info, 'test_asd 0' => $file_info, 'test_1 3' => $file_info];
    var_dump($correct_array == $function_parser->parse_files_functions(
            __DIR__ . "/simple_functions_annotations.php"
        )
    );
}

function simple_functions_call($function_parser) {
    $file_info = FunctionParser::get_file_info(__DIR__ . "/simple_function_calls.php");
    $correct_array = ['test 2' => $file_info, 'my_1_function 1' => $file_info];
    var_dump($correct_array == $function_parser->parse_files_usage_functions(
            __DIR__ . "/simple_function_calls.php"
        )
    );
}

