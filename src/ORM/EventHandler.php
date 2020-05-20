<?php

namespace spaceonfire\BitrixTools\ORM;

use Bitrix\Main\EventManager;
use CIBlockProperty;

class EventHandler
{
    /**
     * Регистрирует сброс кэша инфоблока при действиях над свойствами инфоблока
     * Вызывается автоматически при подключении autoloader.
     */
    public static function boot(): void
    {
        $eventManager = EventManager::getInstance();
        $eventManager->addEventHandler(
            'iblock',
            'OnAfterIBlockPropertyAdd',
            [static::class, 'OnAfterIBlockPropertyAdd']
        );
        $eventManager->addEventHandler(
            'iblock',
            'OnAfterIBlockPropertyUpdate',
            [static::class, 'OnAfterIBlockPropertyUpdate']
        );
        $eventManager->addEventHandler(
            'iblock',
            'OnBeforeIBlockPropertyDelete',
            [static::class, 'OnBeforeIBlockPropertyDelete']
        );
    }

    /**
     * @param $arFields
     * @internal
     */
    public static function OnAfterIBlockPropertyAdd($arFields): void
    {
        static::clearTagCacheByPropertyId($arFields['ID']);
    }

    /**
     * @param $arFields
     * @internal
     */
    public static function OnAfterIBlockPropertyUpdate($arFields): void
    {
        static::clearTagCacheByPropertyId($arFields['ID']);
    }

    /**
     * @param $ID
     * @internal
     */
    public static function OnBeforeIBlockPropertyDelete($ID): void
    {
        static::clearTagCacheByPropertyId($ID);
    }

    /**
     * @param $ID
     * @internal
     */
    private static function clearTagCacheByPropertyId($ID): void
    {
        global $CACHE_MANAGER;

        if (($ID > 0) && $arProperty = CIBlockProperty::GetByID($ID)->Fetch()) {
            $CACHE_MANAGER->ClearByTag('iblock_id_' . $arProperty['IBLOCK_ID']);
        }
    }
}
