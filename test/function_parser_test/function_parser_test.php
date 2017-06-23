<?php
include_once dirname(__FILE__) . '/../../function_parser/FunctionParser.php';

test_function_parser();

function test_function_parser() {
    $function_parser = new \FunctionParser();
    one_function_test($function_parser);
}

function one_function_test($function_parser) {
    $correct_array = ["test 0"];
    var_dump(
        assert(
        $correct_array == $function_parser->parse_files_functions("one_function.php")
        )
    );
}

