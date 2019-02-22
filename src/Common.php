<?php

namespace spaceonfire\BitrixTools;

use Bitrix\Main\Loader;

class Common
{
	/**
	 * Load modules
	 * @param array $modules an array of modules to load
	 * @throws \Bitrix\Main\LoaderException
	 */
	public static function loadModules(array $modules)
	{
		foreach ($modules as $module) {
			if (!Loader::includeModule($module)) {
				throw new \Exception('Could not load ' . $module . ' module');
			}
		}
	}
}
