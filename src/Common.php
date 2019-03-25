<?php

namespace spaceonfire\BitrixTools;

use Bitrix\Main;

class Common
{
	/**
	 * Load modules
	 * @param array $modules an array of modules to load
	 * @throws Main\LoaderException
	 */
	public static function loadModules(array $modules): void
	{
		foreach ($modules as $module) {
			if (!Main\Loader::includeModule($module)) {
				throw new Main\LoaderException('Could not load ' . $module . ' module');
			}
		}
	}

	/**
	 * Add body class to property
	 * @param array $classes an array of class names
	 * @param string $propertyId custom property id, 'BodyClass' by default
	 */
	public static function addBodyClass(array $classes, string $propertyId = 'BodyClass'): void
	{
		global $APPLICATION;
		$arBodyClass = explode(' ', $APPLICATION->GetPageProperty($propertyId, ''));
		$arBodyClass = array_merge($arBodyClass, $classes);
		$arBodyClass = array_unique(array_filter($arBodyClass));
		$APPLICATION->SetPageProperty($propertyId, implode(' ', $arBodyClass));
	}
}
