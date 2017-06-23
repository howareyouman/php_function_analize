<?php
include_once dirname(__FILE__) .  "/cache/Cache.php";

main($argc, $argv);

function main($argc, $argv)
{
    if ($argc != 3) {
        echo "Incorrect number of params!";
        return;
    }

    $directory = __DIR__ . DIRECTORY_SEPARATOR . $argv[1];
    $file = __DIR__ . DIRECTORY_SEPARATOR . $argv[2];

    try {
        $cache = new \Cache($directory);
        $function_parser = new FunctionParser();
        $functions = $function_parser->parse_files_functions($file);
        foreach ($functions as $function) {
            $function_full_name = explode(" ", $function);
            $function_name = $function_full_name[0];
            $function_line = $function->{'line'};
            if (!$cache->check_function($function)) {
                echo get_error_string($function_name, $function_line);
            }
        }
    } catch (PathException $exception) {
        echo $exception;
    }
}

function get_error_string($function_name, $line) {
    return "unused function ". $function_name . " on line " . $line ."\n";
}





