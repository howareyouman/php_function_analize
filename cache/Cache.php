<?php
require_once "../exception/PathException.php";
require_once "../function_parser/FunctionParser.php";

class Cache
{
    const CACHE_FILENAME = "__cache__.json";
    const FILES_USED_IN_CACHE = "__file_index__";
    private $file_path;
    private $cache_map;


    function __construct($path)
    {
        if (file_exists($path)) {
            $this->file_path = $path;
        } else {
            throw new PathException("Path is incorrect - " . $path);
        }
        $this->load_cache($path);
    }

    private function load_cache($path)
    {
        $cache_filename = $path . DIRECTORY_SEPARATOR . self::CACHE_FILENAME;
        $file_index_filename = $path . DIRECTORY_SEPARATOR . self::FILES_USED_IN_CACHE;
        if (file_exists($cache_filename) && file_exists($file_index_filename)) {
            $this->cache_map = json_decode(file_get_contents($path));
            $new_files = $this->get_new_files($path);
            if (!empty($new_files)) {

            }
        } else {
            $this->cache_map = $this->create_cache($this->file_path);
        }
    }

    function store_cache($cache)
    {
        $fp = fopen(self::CACHE_FILENAME, 'w');
        fwrite($fp, json_encode($cache));
        fclose($fp);
    }

    function check_function($function_full_name)
    {
        $files_array = $this->cache_map->{$function_full_name};
        if ($files_array) {
            $spoiled_files = [];
            foreach ($files_array as $file_info) {
                $file_name = $file_info->{'name'};
                $md5 = $file_info->{'md5'};
                $file_last_change_time = $file_info->{'time'};
                if (filemtime($file_name) == $file_last_change_time &&
                    $md5 == md5_file($file_name)
                ) {
                    return true;
                } else {
                    $spoiled_files[] = $file_name;
                }
            }
            $this->cache_map->{$function_full_name} = [];
            $this->update_files($spoiled_files);

            return !empty($this->cache_map->{$function_full_name});

        }

        return false;
    }

    private function get_new_files($path)
    {
        //TODO add recursive files matches *.php pattern + return new ones
        return [];
    }

    private function update_files($filename_list)
    {
        foreach ($filename_list as $filename) {
            $new_functions_in_usage = FunctionParser::parse_files_usage_functions($filename);
            $this->merge_cache($new_functions_in_usage);
        }
    }

    private function create_cache($directory)
    {
        //TODO use get_new_files and create files for cache
        return [];
    }

    private function merge_cache($updated_cache_part)
    {
        //TODO create json merge function with versions
        return [];
    }
}