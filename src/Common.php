<?php

namespace spaceonfire\BitrixTools;

use Bitrix\Main;

class Common
{
    /**
     * Загружает модули 1С-Битрикс
     * @param array $modules Массив модулей, которые необходимо загрузить
     * @throws Main\LoaderException Если какой-нибудь модуль не установлен в системе
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
     * Добавляет классы к body
     * @param array $classes Массив классов
     * @param string $propertyId ID своего свойства для хранения классов body, по-умолчанию, 'BodyClass' для Bitrix24
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
