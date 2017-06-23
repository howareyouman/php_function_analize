<?php

//TODO create simple parser

class FunctionParser
{
    private $file;
    private $current_char;
    const SKIP_CHARS = [" " => "", "\n" => "", "\t" => "", "\r" => "", ";" => ""];

    function __construct()
    {

    }

    private function next()
    {
        if (!feof($this->file)) {
            $this->next_char();
            while (!feof($this->file) && array_key_exists($this->current_char, self::SKIP_CHARS)) {
                $this->next_char();
            }
        }
    }

    private function next_char()
    {
        $this->current_char = fgetc($this->file);
    }

    function parse_files_functions($filename)
    {
        $files_functions = [];
        if (file_exists($filename)) {
            $this->file = fopen($filename, "r");
            $this->skip_php_annotation();
            $this->next();
            while (!feof($this->file)) {
                if (self::is_part_of_word($this->current_char)) {
                    $word = $this->parse_word();
                    if ($word == "function") {
                        $files_functions[] = $this->parse_function_annotation();
                    }
                }
                $this->next();
            }
        }

        return $files_functions;
    }

    private function skip_php_annotation()
    {
        for ($i = 0; $i < 5; $i++) {
            $this->next_char();
        }
    }

    private function parse_word()
    {
        $word = "";
        while (self::is_part_of_word($this->current_char)) {
            $word .= $this->current_char;
            $this->next_char();
        }
        return $word;
    }

    private static function is_part_of_word($char)
    {
        return $char >= "a" && $char <= "z"
            || $char >= "A" && $char <= "Z"
            || $char >= "0" && $char <= "9"
            || $char == "_";
    }

    private function parse_function_annotation()
    {
        $this->next();
        $function_name = $this->parse_word();
        $number_of_args = $this->get_number_of_args();
        $this->parse_function_body();
        //TODO add inner function call
        return $function_name . " " . $number_of_args;
    }

    private function get_number_of_args()
    {
        if ($this->current_char == "(") {
            $args = "";
            $this->next();
            while ($this->current_char != ")") {
                $args .= $this->current_char;
                $this->next();
            }
            if ($args == "") {
                return 0;
            }

            return sizeof(explode(",", $args));
        }
        return false;
    }

    private function parse_function_body()
    {
        if ($this->current_char == "{") {
            $bracket_count = 1;
            while ($bracket_count > 0) {
                $this->next();
                switch ($this->current_char) {
                    case "}" :
                        $bracket_count--;
                        break;
                    case "{" :
                        $bracket_count++;
                        break;
                }

            }
        }
    }


    function parse_files_usage_functions($filename)
    {
        return [];
    }
}