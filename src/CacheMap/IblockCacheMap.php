<?php

namespace spaceonfire\BitrixTools\CacheMap;

use Bitrix\Iblock\IblockTable;
use spaceonfire\BitrixTools\Common;

final class IblockCacheMap extends CacheMap
{
	/** @var static $instance */
	private static $instance;

	private function __construct()
	{
		Common::loadModules(['iblock']);

		$q = IblockTable::query()
			->setSelect(['*'])
			->setFilter(['ACTIVE' => 'Y']);

		parent::__construct($q);
	}

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
	 */
	private function __wakeup()
	{
	}

	public function get($code)
	{
		return static::getInstance()->getDataByCode($code);
	}

	public function getId($code)
	{
		return static::getInstance()->getIdByCode($code);
	}
}
