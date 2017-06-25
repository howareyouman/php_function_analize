<?php
include_once __DIR__ . "/../exception/PathException.php";
include_once __DIR__ . "/../function_parser/FunctionParser.php";

class Cache
{
    const CACHE_FILENAME = "__cache__.json";
    const FILES_USED_IN_CACHE = "__file_index__.json";
    const PHP_FILE_PATTERN = "/^.*\.php/";

    private $file_path;
    private $cache_map;
    private $files_in_use;
    private $function_parser;


    function __construct($path)
    {
        if (file_exists($path)) {
            $this->file_path = $path;
        } else {
            throw new \PathException("Path is incorrect - " . $path);
        }
        $this->function_parser = new \FunctionParser();
        $this->load_cache($path);
        $this->store_cache($path);
    }

    private function load_cache($path)
    {
        $cache_filename = $path . DIRECTORY_SEPARATOR . self::CACHE_FILENAME;
        $file_index_filename = $path . DIRECTORY_SEPARATOR . self::FILES_USED_IN_CACHE;
        if (file_exists($cache_filename) && file_exists($file_index_filename)) {
            $this->cache_map = json_decode(file_get_contents($path));
            $this->files_in_use = json_decode(file_get_contents($file_index_filename));

            $new_files = $this->get_new_files($path);
            if (!empty($new_files)) {
                array_merge($this->files_in_use, $new_files);
                $this->update_files($new_files);
            }
        } else {
            $this->files_in_use = [];
            $this->cache_map = [];
            $this->create_cache($this->file_path);
        }
    }

    private function store_cache($path)
    {
        $fp = fopen($path . DIRECTORY_SEPARATOR . self::CACHE_FILENAME, 'w');
        fwrite($fp, json_encode($this->cache_map));
        fclose($fp);

        $fp = fopen($path . DIRECTORY_SEPARATOR . self::FILES_USED_IN_CACHE, 'w');
        fwrite($fp, json_encode($this->files_in_use));
        fclose($fp);

    }

    public function check_function($function_full_name)
    {
        $files_array = $this->cache_map[$function_full_name];
        if ($files_array) {
            $spoiled_files = [];
            foreach ($files_array as $file_info) {
                $file_name = $file_info["name"];
                $md5 = $file_info["md5_hash"];
                $file_last_change_time = $file_info["time"];
                if (filemtime($file_name) == $file_last_change_time &&
                    $md5 == md5_file($file_name)
                ) {
                    return true;
                } else {
                    $spoiled_files[$file_name] = $file_name;
                }
            }

            $this->cache_map[$function_full_name] = [];

            //instead of set
            $this->update_files(array_keys($spoiled_files));

            return !empty($this->cache_map->{$function_full_name});

        }

        return false;
    }

    private function get_new_files($path)
    {
        $files_and_directories = scandir($path);
        $new_files = [];
        foreach ($files_and_directories as $element) {
            $full_path = $path . DIRECTORY_SEPARATOR . $element;
            if (!is_dir($element)) {
                if (preg_match(self::PHP_FILE_PATTERN, $element) &&
                    !array_key_exists($full_path, $this->files_in_use)
                ) {
                    $new_files[$full_path] = $full_path;
                }
            } else {
                if ($element != '.' && $element != '..') {
                    array_merge($new_files, $this->get_new_files($full_path));
                }
            }
        }
        return $new_files;
    }

    private function update_files($filename_list)
    {
        foreach ($filename_list as $filename) {
            $new_functions_in_usage = $this->function_parser->parse_files_usage_functions($filename);
            $this->merge_cache($new_functions_in_usage);
        }
    }

    private function create_cache($directory)
    {
        $this->files_in_use = $this->get_new_files($directory);
        foreach ($this->files_in_use as $file) {
            $this->merge_cache($this->function_parser->parse_files_usage_functions($file));
        }
    }

    private function merge_cache($updated_cache_part)
    {
        foreach (array_keys($updated_cache_part) as $function) {
            if (!array_key_exists($function, $this->cache_map)) {
                $this->cache_map[$function] = [$updated_cache_part[$function]];
            } else {

                $old_usage = $this->map_with_filename(
                    $this->cache_map[$function]
                );

                $new_usage = $this->map_with_filename(
                    $updated_cache_part[$function]
                );

                $new_file_array = [];

                foreach ($old_usage as $old_file) {
                    if (array_key_exists($old_file, $new_usage)) {
                        if ($old_usage[$old_file]->{'time'} < $new_usage[$old_file]->{'time'}) {
                            $new_file_array[] = $new_usage[$old_file]->{'element'};
                        } else {
                            $new_file_array[] = $old_usage[$old_file]->{'element'};
                        }
                    } else {
                        $new_file_array[] = $old_usage[$old_file]->{'element'};
                    }
                }

                foreach ($new_usage as $new_file) {
                    if (!array_key_exists($new_file, $old_usage)) {
                        $new_file_array[] = $new_usage[$new_file]->{'element'};
                    }
                }
                $this->cache_map[$function] = $new_file_array;
            }
        }
    }

    private function map_with_filename($file_list)
    {
        $mapped_elements = [];
        foreach ($file_list as $file) {
            $mapped_elements[$file->{'name'}] = array(
                'time' => $file->{'time'},
                'element' => $file_list[$file]
            );
        }
        return $mapped_elements;
    }
}