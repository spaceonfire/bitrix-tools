<?php

use spaceonfire\BitrixTools\CacheMap\CacheMapOptions;
use spaceonfire\BitrixTools\CacheMap\CustomCacheMap;

$cacheMap = new CustomCacheMap(function () {
    return [['id' => 42, 'code' => 'test']];
}, new CacheMapOptions('my-unique-id', 'id', 'code'));

var_dump($cacheMap->getId('test'));
