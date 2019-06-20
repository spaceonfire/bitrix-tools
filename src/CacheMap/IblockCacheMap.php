<?php

namespace spaceonfire\BitrixTools\CacheMap;

use Bitrix\Iblock\IblockTable;
use Bitrix\Main;
use Bitrix\Main\ORM\Query\Query;
use spaceonfire\BitrixTools\Common;

final class IblockCacheMap implements CacheMapStaticInterface
{
	use CacheMapTrait, CacheMapSingleton;

	/**
	 * IblockCacheMap constructor.
	 * @throws Main\LoaderException
	 * @throws Main\SystemException
	 */
	private function __construct()
	{
		Common::loadModules(['iblock']);

		/** @var Query $q */
		$q = IblockTable::query()
			->setSelect(['*'])
			->setFilter([
				'ACTIVE' => 'Y',
				'!CODE' => false,
			]);

		$this->traitConstruct($q);
	}
}
