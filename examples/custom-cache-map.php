<?php

use spaceonfire\BitrixTools\CacheMap\CustomCacheMap;
use spaceonfire\BitrixTools\CacheMap\CacheMapOptions;

$map = new CustomCacheMap(function () {
	return [['id' => 42, 'code' => 'test']];
}, new CacheMapOptions('my-unique-id', 'id', 'code'));

var_dump($map->getId('test'));
