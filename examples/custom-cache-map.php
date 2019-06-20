<?php

use spaceonfire\BitrixTools\CacheMap\CustomCacheMap;

$map = new CustomCacheMap(function () {
	return [['ID' => 12, 'CODE' => 'test']];
});

var_dump($map->getId('test'));
