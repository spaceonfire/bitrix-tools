<?php

namespace spaceonfire\BitrixTools;

use Bitrix\Main\ArgumentTypeException;

class ArrayTools
{
	/**
	 * Конвертирует вложенный ассоциативный массив в одноуровневый
	 * @param array $array Исходный вложенный массив
	 * @param string $separator Строка для склеивания ключей, по-умолчанию '.'
	 * @param string $prefix Префикс для ключей, в основном нужен для рекурсивных вызовов, по-умолчанию пустая строка
	 * @return array Одноуровневый массив
	 */
	public static function flatten(array $array, $separator = '.', $prefix = ''): array
	{
		$result = [];
		foreach ($array as $key => $item) {
			$prefixedKey = ($prefix ? $prefix . $separator : '') . $key;

			if (static::isArrayAssoc($item)) {
				$childFlaten = self::flatten($item, $separator, $prefixedKey);
				foreach ($childFlaten as $childKey => $childValue) {
					$result[$childKey] = $childValue;
				}
			} else {
				$result[$prefixedKey] = $item;
			}
		}
		return $result;
	}

	/**
	 * Конвертирует одноуровневый ассоциативный массив во вложенный, разбивая ключи по $separator
	 * @param array $array Исходный одноуровневый ассоциативный массив
	 * @param string $separator Подстрока для разбивки ключей, по-умолчанию '.'
	 * @return array Многоуровневый массив
	 */
	public static function unflatten(array $array, $separator = '.'): array
	{
		$nestedKeys = array_filter(array_keys($array), function ($key) use ($separator) {
			return strpos($key, $separator) !== false;
		});
		if (!count($nestedKeys)) {
			return $array;
		}
		foreach ($nestedKeys as $key) {
			$prefix = explode($separator, $key);
			$field = array_pop($prefix);
			$prefix = implode($separator, $prefix);
			$array[$prefix][$field] = $array[$key];
			unset($array[$key]);
		}
		return self::unflatten($array, $separator);
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
	 * Проверяет, является ли массив ассоциативный (есть хотябы один строковый ключ)
	 * @param mixed $var Переменная для проверки
	 * @return bool
	 */
	public static function isArrayAssoc($var): bool
	{
		if (!is_array($var)) {
			return false;
		}

		$i = 0;
		foreach ($var as $k => $v) {
			if ('' . $k !== '' . $i) {
				return true;
			}
			$i++;
		}

		return false;
	}

	/**
	 * Рекурсивный мерж нескольких массивов
	 * @param array ...$arrays
	 * @return array
	 * @throws ArgumentTypeException
	 */
	public static function merge(...$arrays): array
	{
		foreach ($arrays as $array) {
			if (!is_array($array)) {
				throw new ArgumentTypeException('arrayN', 'array');
			}
		}

		$ret = array_shift($arrays);

		while (!empty($arrays)) {
			foreach (array_shift($arrays) as $k => $v) {
				if (is_int($k)) {
					if (array_key_exists($k, $ret)) {
						$ret[] = $v;
					} else {
						$ret[$k] = $v;
					}
				} elseif (is_array($v) && isset($ret[$k]) && is_array($ret[$k])) {
					$ret[$k] = static::merge($ret[$k], $v);
				} else {
					$ret[$k] = $v;
				}
			}
		}

		return $ret;
	}
}
