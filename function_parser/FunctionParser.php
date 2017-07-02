<?php

class FunctionParser
{
    private $file;
    private $current_char;
    private $line_count;
    const SKIP_CHARS = [" " => "", "\n" => "", "\t" => "", "\r" => "", ";" => ""];
    const SKIP_LINE = ["include_once" => "", "require_once" => "", "require" => "", "include" => ""];

    function __construct()
    {
        $this->line_count = 1;
    }

    private function next()
    {
        if (!feof($this->file)) {
            $this->next_char();
            while (!feof($this->file) && array_key_exists($this->current_char, self::SKIP_CHARS)) {
                if ($this->current_char == "\n") {
                    $this->line_count++;
                }
                $this->next_char();
            }
        }
    }

    private function skip_to_char($char)
    {
        while ($this->current_char != $char) {
            $this->next_char();
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
            $file_info = $this->get_file_info($filename);
            $this->file = fopen($filename, "r");
            $this->skip_php_annotation();
            $this->next();
            while (!feof($this->file)) {
                if (self::is_part_of_word($this->current_char)) {
                    $word = $this->parse_word();
                    if ($word == "function") {
                        $function_info = $file_info;
                        $function_info['line'] = $this->line_count;
                        $files_functions[$this->parse_function_annotation()] = $function_info;
                    }
                }
                $this->next();
            }
            fclose($this->file);
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
            || $char == "_" || $char == "$";
    }

    private function parse_function_annotation()
    {
        $this->next();
        $function_name = $this->parse_word();

        if ($this->current_char != "(") {
            $this->next();
        }

        $number_of_args = $this->get_number_of_args();
        $this->next();

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
        $files_usage_functions = [];
        $file_info = $this->get_file_info($filename);
        if (file_exists($filename)) {
            $this->file = fopen($filename, "r");
            $this->skip_php_annotation();
            $this->next();
            while (!feof($this->file)) {
                if (self::is_part_of_word($this->current_char)) {
                    $word = $this->parse_word();
                    if ($word == "function") {
                        $this->parse_function_annotation();
                    } else {
                        if (array_key_exists($word, self::SKIP_LINE)) {
                            $this->skip_to_char("\n");
                        } else {
                            if ($word[0] != "$") {
                                $number_of_args = $this->get_number_of_args();
                                if ($number_of_args >= 0) {
                                    $files_usage_functions[$word . " " . $number_of_args] = $file_info;
                                }
                            }
                        }
                    }
                }
                $this->next();
            }
            fclose($this->file);
        }

        return $files_usage_functions;
    }

    public static function get_file_info($filename)
    {
        $file_info = [];
        $file_info['name'] = $filename;
        $file_info['md5_hash'] = md5_file($filename);
        $file_info['time'] = filemtime($filename);
        return $file_info;
    }
}