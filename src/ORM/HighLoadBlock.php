<?php

namespace spaceonfire\BitrixTools\ORM;

use Bitrix\Highloadblock as HL;
use Bitrix\Main\EventManager;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM;
use spaceonfire\BitrixTools;

Loc::loadMessages(__FILE__);

/**
 * Class HighLoadBlock
 *
 * @method static ORM\Data\AddResult add(array $data)
 * @method static void checkFields(ORM\Data\Result $result, $primary, array $data)
 * @method static ORM\Data\DeleteResult delete($primary)
 * @method static ORM\Query\Result getById($id)
 * @method static ORM\Query\Result getByPrimary($id)
 * @method static int getCount($filter = array(), array $cache = array())
 * @method static ORM\Query\Result getList(array $parameters = array())
 * @method static array getMap()
 * @method static string getTableName()
 * @method static string getUfId()
 * @method static ORM\Query\Query query()
 * @method static ORM\Data\UpdateResult update($primary, array $data)
 */
abstract class HighLoadBlock
{
	/** @var ORM\Entity[] $entity */
	protected static $entities = [];

	/**
	 * @return array|int|string
	 */
	abstract public static function getHLId();

	/**
	 * Returns compiled entity for highload block
	 * @return ORM\Entity
	 * @throws \Bitrix\Main\LoaderException
	 * @throws \Bitrix\Main\SystemException
	 */
	public static function getEntity(): ORM\Entity
	{
		BitrixTools\Common::loadModules(['highloadblock']);

		if (!static::$entities[static::getHLId()]) {
			static::$entities[static::getHLId()] = HL\HighloadBlockTable::compileEntity(static::getHLId());
			static::registerEvents();
		}

		return static::$entities[static::getHLId()];
	}

	/**
	 * Register entity event handlers
	 * @throws \Bitrix\Main\LoaderException
	 * @throws \Bitrix\Main\SystemException
	 */
	protected static function registerEvents(): void
	{
		$entity = self::getEntity();
		$dataClass = $entity->getDataClass();
		$eventManager = EventManager::getInstance();

		$events = [
			$dataClass::EVENT_ON_BEFORE_ADD,
			$dataClass::EVENT_ON_ADD,
			$dataClass::EVENT_ON_AFTER_ADD,
			$dataClass::EVENT_ON_BEFORE_UPDATE,
			$dataClass::EVENT_ON_UPDATE,
			$dataClass::EVENT_ON_AFTER_UPDATE,
			$dataClass::EVENT_ON_BEFORE_DELETE,
			$dataClass::EVENT_ON_DELETE,
			$dataClass::EVENT_ON_AFTER_DELETE,
		];

		$eventNamespace = $entity->getNamespace() . $entity->getName();

		foreach ($events as $event) {
			$method = lcfirst($event);
			if (is_callable([static::class, $method])) {
				$eventManager->addEventHandler('', $eventNamespace . '::' . $event, [static::class, $method]);
			}
		}

		// Clean cache on modifications
		$events = [
			$dataClass::EVENT_ON_AFTER_ADD,
			$dataClass::EVENT_ON_AFTER_UPDATE,
			$dataClass::EVENT_ON_AFTER_DELETE,
		];
		foreach ($events as $event) {
			$eventManager->addEventHandler('', $eventNamespace . '::' . $event, [static::class, 'cleanEntityCache']);
		}
	}

	/**
	 * Clean cache
	 */
	public static function cleanEntityCache(): void
	{
		try {
			$GLOBALS['CACHE_MANAGER']->ClearByTag(static::class);
			self::getEntity()->cleanCache();
		} catch (\Throwable $err) {
		}
	}

	/**
	 * Proxy method calls to compiled entity data class
	 * @param string $name method name
	 * @param array $arguments arguments
	 * @return mixed
	 * @throws \Bitrix\Main\LoaderException
	 * @throws \Bitrix\Main\SystemException
	 */
	public static function __callStatic($name, $arguments)
	{
		$result = call_user_func_array([self::getEntity()->getDataClass(), $name], $arguments);

		// Tag cache for querying requests
		if ($result instanceof ORM\Query\Result || $name === 'getCount') {
			if (defined('BX_COMP_MANAGED_CACHE') && is_object($GLOBALS['CACHE_MANAGER'])) {
				$GLOBALS['CACHE_MANAGER']->RegisterTag(static::class);
			}
		}

		return $result;
	}
}
