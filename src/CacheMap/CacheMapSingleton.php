<?php

namespace spaceonfire\BitrixTools\CacheMap;

trait CacheMapSingleton
{
	/** @var static $instance */
	private static $instance;

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
	private function __wakeup()
	{
	}

	public static function get($code)
	{
		return static::getInstance()->getDataByCode($code);
	}

	public static function getId($code)
	{
		return static::getInstance()->getIdByCode($code);
	}

	public static function clearCache()
	{
		return self::getInstance()->traitClearCache();
	}
}
