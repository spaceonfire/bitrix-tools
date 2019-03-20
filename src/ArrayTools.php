<?php

namespace spaceonfire\BitrixTools;

use Bitrix\Main\ArgumentTypeException;

class ArrayTools
{
	/**
	 * Convert a multi-dimensional array into a single-dimensional array
	 * @param array $array Source multi-dimensional array
	 * @param string $separator Glue string for imploding keys
	 * @param string $prefix Key prefix, mostly needed for recursive call
	 * @return array single-dimensional array
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
	 * Convert single-dimensional associative array to multi-dimensional by splitting keys with separator
	 * @param array $array Source single-dimensional array
	 * @param string $separator Glue string for exploding keys
	 * @return array multi-dimensional array
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
	 * Remove keys started with tilda (~)
	 * @param array $data
	 * @return array
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
	 * Check that array is associative (have at least one string key)
	 * @param mixed $var variable to check
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
	 * Recursive merge multiple arrays
	 * @param array ...$arrays
	 * @return array
	 * @throws ArgumentTypeException
	 */
	public static function merge(...$arrays): array
	{
		foreach ($arrays as &$array) {
			if (!is_array($array)) {
				throw new ArgumentTypeException('arrayN', 'array');
			}

			$array = static::flatten($array);
		}
		unset($array);
		$ret = array_merge_recursive(...$arrays);
		$ret = static::unflatten($ret);
		return $ret;
	}
}
