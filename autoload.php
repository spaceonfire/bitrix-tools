<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	return;
}

use spaceonfire\BitrixTools\CacheMap;
use spaceonfire\BitrixTools\ORM;

try {
	CacheMap\IblockCacheMap::register();
	CacheMap\HighloadBlockCacheMap::register();
	CacheMap\UserGroupCacheMap::register();
	ORM\EventHandler::boot();
} catch (\Throwable $err) {
	Bitrix\Main\Application::getInstance()->getExceptionHandler()->writeToLog($err);
}
