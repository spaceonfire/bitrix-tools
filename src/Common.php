<?php

namespace spaceonfire\BitrixTools;

use Bitrix\Main;
use CApplicationException;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

abstract class Common
{
    final private function __construct()
    {
    }

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

    /**
     * Converts bitrix $APPLICATION global exception message to throwable
     * @param string $defaultErrorMessage
     * @param string $className
     * @return Throwable
     */
    public static function getAppException(string $defaultErrorMessage = 'Error', string $className = RuntimeException::class): Throwable
    {
        global $APPLICATION;

        if (!is_subclass_of($className, Throwable::class)) {
            throw new InvalidArgumentException('Exception class name must implement Throwable interface');
        }

        $appException = $APPLICATION->GetException();

        if (is_string($appException)) {
            $errorMessage = $appException;
        }

        if ($appException instanceof CApplicationException) {
            $errorMessage = $appException->GetString();
        }

        return new $className($errorMessage ?? $defaultErrorMessage);
    }

}
