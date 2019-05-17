<?php

namespace spaceonfire\BitrixTools\ORM;

class EventHandler
{
	/**
	 * Регистрирует сброс кэша инфоблока при действиях над свойствами инфоблока
	 */
	public static function boot(): void
	{
		$eventManager = \Bitrix\Main\EventManager::getInstance();
		$eventManager->addEventHandler('iblock', 'OnAfterIBlockPropertyAdd', [static::class, 'OnAfterIBlockPropertyAdd']);
		$eventManager->addEventHandler('iblock', 'OnAfterIBlockPropertyUpdate', [static::class, 'OnAfterIBlockPropertyUpdate']);
		$eventManager->addEventHandler('iblock', 'OnBeforeIBlockPropertyDelete', [static::class, 'OnBeforeIBlockPropertyDelete']);
	}

	public static function OnAfterIBlockPropertyAdd($arFields): void
	{
		static::clearTagCacheByPropertyId($arFields['ID']);
	}

	public static function OnAfterIBlockPropertyUpdate($arFields): void
	{
		static::clearTagCacheByPropertyId($arFields['ID']);
	}

	public static function OnBeforeIBlockPropertyDelete($ID): void
	{
		static::clearTagCacheByPropertyId($ID);
	}

	private static function clearTagCacheByPropertyId($ID): void
	{
		global $CACHE_MANAGER;

		if (($ID > 0) && $arProperty = \CIBlockProperty::GetByID($ID)->Fetch()) {
			$CACHE_MANAGER->ClearByTag('iblock_id_' . $arProperty['IBLOCK_ID']);
		}
	}
}
