<?php

namespace spaceonfire\BitrixTools\CacheMap;

use Bitrix\Main;
use Bitrix\Main\ORM\Data\DataManager;
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

	/**
	 * Регистрация обработчиков событий для очистки кэша при изменении сущности
	 * @throws Main\SystemException
	 */
	public static function register(): void
	{
		$eventManager = Main\EventManager::getInstance();

		$ormEntity = Main\GroupTable::getEntity();

		$eventsTree = [
			'main' => [
				'OnAfterGroupAdd' => 1,
				'OnAfterIBlockUpdate' => 1,
				'OnAfterIBlockDelete' => 1,
				$ormEntity->getNamespace() . $ormEntity->getName() . '::' . DataManager::EVENT_ON_AFTER_ADD => 2,
				$ormEntity->getNamespace() . $ormEntity->getName() . '::' . DataManager::EVENT_ON_AFTER_UPDATE => 2,
				$ormEntity->getNamespace() . $ormEntity->getName() . '::' . DataManager::EVENT_ON_AFTER_DELETE => 2,
			],
		];

		foreach ($eventsTree as $moduleId => $events) {
			foreach ($events as $event => $version) {
				if ($version === 2) {
					$eventManager->addEventHandler($moduleId, $event, [static::class, 'clearCache']);
				} else {
					$eventManager->addEventHandlerCompatible($moduleId, $event, [static::class, 'clearCache']);
				}
			}
		}
	}
}
