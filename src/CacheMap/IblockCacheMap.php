<?php

namespace spaceonfire\BitrixTools\CacheMap;

use Bitrix\Iblock\IblockTable;
use Bitrix\Main;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Query\Query;
use spaceonfire\BitrixTools\Common;

/**
 * Класс IblockCacheMap позволяет получить информацию об инфоблоке по его символьному коду из кэша
 * @package spaceonfire\BitrixTools\CacheMap
 */
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

	/**
	 * Регистрация обработчиков событий для очистки кэша при изменении сущности
	 * Вызывается автоматически при подключении autoloader.
	 * @throws Main\SystemException
	 */
	public static function register(): void
	{
		try {
			Common::loadModules(['iblock']);
		} catch (\Throwable $err) {
			return;
		}

		$eventManager = Main\EventManager::getInstance();

		$ormEntity = IblockTable::getEntity();

		$eventsTree = [
			'iblock' => [
				'OnAfterIBlockAdd' => 1,
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
