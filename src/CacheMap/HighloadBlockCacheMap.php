<?php

namespace spaceonfire\BitrixTools\CacheMap;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main;
use Bitrix\Main\ORM\Query\Query;
use spaceonfire\BitrixTools\Common;

final class HighloadBlockCacheMap implements CacheMapStaticInterface
{
	use CacheMapTrait, CacheMapSingleton;

	/**
	 * HighloadBlockCacheMap constructor.
	 * @throws Main\LoaderException
	 * @throws Main\SystemException
	 */
	private function __construct()
	{
		Common::loadModules(['highloadblock']);

		/** @var Query $q */
		$q = HighloadBlockTable::query()
			->setSelect(['*'])
			->setFilter(['!NAME' => false]);

		$this->traitConstruct($q, 'ID', 'NAME');
	}
}
