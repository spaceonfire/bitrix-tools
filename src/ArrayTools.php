<?php

namespace spaceonfire\BitrixTools;

class ArrayTools
{
	/**
	 * Convert a multi-dimensional array into a single-dimensional array
	 * @param array $array Source multi-dimensional array
	 * @param string $separator Glue string for imploding keys
	 * @param string $prefix Key prefix, mostly needed for recursive call
	 * @return array single-dimensional array
	 */
	public static function flatten(array $array, $separator = '.', $prefix = '')
	{
		$result = [];
		foreach ($array as $key => $item) {
			$prefixedKey = ($prefix ? $prefix . $separator : '') . $key;

			if (is_array_assoc($item)) {
				$result = array_merge($result, self::flatten($item, $separator, $prefixedKey));
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
	public static function unflatten(array $array, $separator = '.')
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
	public static function removeTildaKeys(array $data) {
		$deleteKeys = array_filter(array_keys($data), function($key) {
			return strpos($key, '~') === 0;
		});
		foreach ($deleteKeys as $key) unset($data[$key]);
		return $data;
	}
}
