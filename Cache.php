<?php
require_once "./exception/PathException.php";

class Cache
{
    const CACHE_FILENAME = "__cache__";
    private $path;

    function __construct($path)
    {
        if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . $path)) {
            $this->path = $path;
        } else {
            throw new PathException("Path is incorrect - " . __DIR__ . DIRECTORY_SEPARATOR . $path);
        }
    }

    function load_cache($path)
    {

    }

    function store_cache($cache)
    {

    }
}