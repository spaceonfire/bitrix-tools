<?php

namespace spaceonfire\BitrixTools\CacheMap;

use Bitrix\Main;
use Bitrix\Main\ORM\Query\Query;

final class UserGroupCacheMap implements CacheMapStaticInterface
{
	use CacheMapTrait, CacheMapSingleton;

	/**
	 * UserGroupCacheMap constructor.
	 * @throws Main\SystemException
	 */
	private function __construct()
	{
		/** @var Query $q */
		$q = Main\GroupTable::query()
			->setSelect(['*'])
			->setFilter([
				'ACTIVE' => 'Y',
				'!STRING_ID' => false,
			]);

		$this->traitConstruct($q, 'ID', 'STRING_ID');
	}
}
