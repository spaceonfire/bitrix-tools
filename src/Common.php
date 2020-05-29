<?php

namespace spaceonfire\BitrixTools;

use Bitrix\Main;
use Bitrix\Main\Context;
use Bitrix\Main\EventManager;
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
     */
    public static function loadModules(array $modules): void
    {
        foreach ($modules as $module) {
            try {
                if (!Main\Loader::includeModule($module)) {
                    throw new RuntimeException('Could not load ' . $module . ' module');
                }
            } catch (Main\LoaderException $e) {
                throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
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
     * Конвертирует сообщение об ошибке из глобального $APPLICATION в исключение
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

    /**
     * Возвращает ID модуля из полного имени класса
     *
     * @param string $fqn
     * @return string
     * @example
     * ```php
     * Common::getModuleIdByFqn('\Bitrix\Main\Loader') === 'main';
     * Common::getModuleIdByFqn('\Bitrix\Iblock\Type') === 'iblock';
     * Common::getModuleIdByFqn('\Vendor\Module\Class') === 'vendor.module';
     * Common::getModuleIdByFqn('\SomeRootClass') === 'main';
     * ```
     *
     */
    public static function getModuleIdByFqn(string $fqn): string
    {
        [$vendor, $module] = explode('\\', ltrim($fqn, '\\')) + [null, null];

        if (empty($vendor) || empty($module)) {
            return 'main';
        }

        $vendor = strtolower($vendor);
        $module = strtolower($module);

        if ($vendor === 'bitrix') {
            return $module;
        }

        return $vendor . '.' . $module;
    }

    /**
     * Фикс функции LocalRedirect при запуске проекта за прокси-сервером на нестандартных портах.
     * @see LocalRedirect()
     */
    public static function trustProxy(): void
    {
        EventManager::getInstance()->addEventHandlerCompatible(
            'main',
            'OnBeforeLocalRedirect',
            static function (&$url, $skipSecurityCheck, &$isExternal) {
                if ((bool)$isExternal) {
                    return;
                }

                $req = Context::getCurrent()->getRequest();
                $host = $req->getHttpHost();
                $proto = 'http' . ($req->isHttps() ? 's' : '');
                $url = $proto . '://' . $host . $url;

                global $APPLICATION;
                $APPLICATION->StoreCookies();

                $isExternal = true;
            }
        );
    }

    /**
     * Отключает вход в Битрикс по HTTP авторизации
     * @see CUser::LoginByHttpAuth()
     */
    public static function disableHttpAuth(): void
    {
        EventManager::getInstance()->addEventHandlerCompatible(
            'main',
            'onBeforeUserLoginByHttpAuth',
            static function (&$auth) {
                return false;
            }
        );
    }
}
