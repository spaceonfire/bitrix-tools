<?php

namespace spaceonfire\BitrixTools\CacheMap;

use Bitrix\Main;

trait CacheMapSingleton
{
    /** @var static */
    private static $instance;

    /**
     * Возвращает экземпляр класса
     * @return static
     */
    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * prevent the instance from being cloned (which would create a second instance of it)
     */
    private function __clone()
    {
    }

    /**
     * prevent from being unserialized (which would create a second instance of it)
     * @noinspection MagicMethodsValidityInspection
     */
    private function __wakeup(): void
    {
    }

    /**
     * Возвращает данные элемента по символьному коду
     * @param string $code символьный код
     * @return array|null
     */
    public static function get($code)
    {
        return static::getInstance()->getDataByCode($code);
    }

    /**
     * Возвращает ID элемента по символьному коду
     * @param string $code символьный код
     * @return int|mixed ID элемента, по возможности будет приведен к целочисленному типу
     */
    public static function getId($code)
    {
        return static::getInstance()->getIdByCode($code);
    }

    /**
     * Очищает кэш
     * @throws Main\ArgumentNullException
     */
    public static function clearCache()
    {
        return self::getInstance()->traitClearCache();
    }
}
