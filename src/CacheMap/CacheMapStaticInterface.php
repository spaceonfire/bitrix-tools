<?php

namespace spaceonfire\BitrixTools\CacheMap;

interface CacheMapStaticInterface
{
    public static function get($code);

    public static function getId($code);

    public static function clearCache();
}
