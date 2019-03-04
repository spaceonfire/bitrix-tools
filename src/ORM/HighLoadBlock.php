<?php

namespace spaceonfire\BitrixTools\ORM;

use Bitrix\Highloadblock as HL;
use Bitrix\Main\EventManager;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM;

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
	abstract static function getHLId();

	public static function getEntity()
	{
		if (!array_key_exists(static::getHLId(), static::$entities)) {
			static::$entities[static::getHLId()] = HL\HighloadBlockTable::compileEntity(static::getHLId());
			static::registerEvents();
		}
		return static::$entities[static::getHLId()];
	}

	protected static function compile()
	{
		self::getEntity();
	}

	protected static function registerEvents()
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
			if (is_callable([get_called_class(), $method])) {
				$eventManager->addEventHandler('', $eventNamespace . '::' . $event, [get_called_class(), $method]);
			}
		}

		// Clean cache on modifications
		$events = [
			$dataClass::EVENT_ON_AFTER_ADD,
			$dataClass::EVENT_ON_AFTER_UPDATE,
			$dataClass::EVENT_ON_AFTER_DELETE,
		];
		foreach ($events as $event) {
			$eventManager->addEventHandler('', $eventNamespace . '::' . $event, [get_called_class(), 'cleanEntityCache']);
		}
	}

	public static function cleanEntityCache()
	{
		try {
			$GLOBALS['CACHE_MANAGER']->ClearByTag(get_called_class());
			self::getEntity()->cleanCache();
		} catch (\Throwable $err) {}
	}

	public static function __callStatic($name, $arguments)
	{
		static::compile();
		$result = call_user_func_array([self::getEntity()->getDataClass(), $name], $arguments);

		// Tag cache for querying requests
		if ($result instanceof ORM\Query\Result || $name === 'getCount') {
			if (defined('BX_COMP_MANAGED_CACHE') && is_object($GLOBALS['CACHE_MANAGER'])) {
				$GLOBALS['CACHE_MANAGER']->RegisterTag(get_called_class());
			}
		}

		return $result;
	}
}
