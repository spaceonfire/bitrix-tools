<?php

namespace spaceonfire\BitrixTools;

use spaceonfire\Collection\ArrayHelper;

abstract class ArrayTools extends ArrayHelper
{
    final private function __construct()
    {
    }

    /**
     * Удаляет из ассоциативного массива ключи, начинающиеся с тильды (~)
     * @param array $data Исходный ассоциативный массив с данными
     * @return array Массив с удаленными ключами
     */
    public static function removeTildaKeys(array $data): array
    {
        $deleteKeys = array_filter(array_keys($data), function ($key) {
            return strpos($key, '~') === 0;
        });
        foreach ($deleteKeys as $key) {
            unset($data[$key]);
        }
        return $data;
    }

    /**
     * Конвертирует вложенный ассоциативный массив в одноуровневый
     * @param array $array Исходный вложенный массив
     * @param string $separator Строка для склеивания ключей, по-умолчанию '.'
     * @param string $prefix Префикс для ключей, в основном нужен для рекурсивных вызовов, по-умолчанию пустая строка
     * @return array Одноуровневый массив
     */
    public static function flatten(array $array, $separator = '.', $prefix = ''): array
    {
        return parent::flatten(...func_get_args());
    }

    /**
     * Конвертирует одноуровневый ассоциативный массив во вложенный, разбивая ключи по $separator
     * @param array $array Исходный одноуровневый ассоциативный массив
     * @param string $separator Подстрока для разбивки ключей, по-умолчанию '.'
     * @return array Многоуровневый массив
     */
    public static function unflatten(array $array, $separator = '.'): array
    {
        return parent::unflatten(...func_get_args());
    }

    /**
     * Проверяет, является ли массив ассоциативный (есть хотябы один строковый ключ)
     * @param mixed $var Переменная для проверки
     * @return bool
     */
    public static function isArrayAssoc($var): bool
    {
        return parent::isArrayAssoc(...func_get_args());
    }

    /**
     * Рекурсивный мерж нескольких массивов
     * @param array ...$arrays
     * @return array
     */
    public static function merge(...$arrays): array
    {
        return parent::merge(...func_get_args());
    }
}
