<?php

namespace spaceonfire\BitrixTools\CacheMap;

interface CacheMapInterface
{
	public function get($code);
	public function getId($code);
	public function clearCache();
}