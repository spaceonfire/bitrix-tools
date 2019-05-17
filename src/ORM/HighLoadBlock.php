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
	 * @return int|string
	 */
	abstract public static function getHLId();

	/**
	 * Возвращает скомпилированную сущность HighLoad блока
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
	 * Регистрирует обработчики событий сущности
	 * @throws \Bitrix\Main\LoaderException
	 * @throws \Bitrix\Main\SystemException
	 */
	protected static function registerEvents(): void
	{
		$entity = static::getEntity();
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
	 * Очистка кэша по сущности
	 */
	public static function cleanEntityCache(): void
	{
		try {
			$GLOBALS['CACHE_MANAGER']->ClearByTag(static::class);
			static::getEntity()->cleanCache();
		} catch (\Throwable $err) {
		}
	}

	/**
	 * Расширяет параметры запроса
	 *
	 * В то время, как наследники данного класса являются просто прокси к скомпилированной сущности
	 * HighLoad блока, мы не можем просто переопределить метод `getMap()`. Но мы можем расширять поля
	 * `runtime` и другие параметры запроса переопределив данный метод.
	 *
	 * @param array $parameters
	 * @return array
	 */
	public static function mergeOrmParameters(array $parameters = [])
	{
		return $parameters;
	}

	/**
	 * Проксируем вызовы методов в data class скомпилированной сущности
	 * @param string $name method name
	 * @param array $arguments arguments
	 * @return mixed
	 * @throws \Bitrix\Main\LoaderException
	 * @throws \Bitrix\Main\SystemException
	 */
	public static function __callStatic($name, $arguments)
	{
		// Merge orm params before read methods call
		if (in_array($name, ['getList', 'getById', 'getByPrimary'], true)) {
			$i = $name === 'getList' ? 0 : 1;
			if (!isset($arguments[$i]) || !is_array($arguments[$i])) {
				$arguments[$i] = [];
			}

			if (is_callable([static::class, 'mergeOrmParameters'])) {
				$arguments[$i] = static::mergeOrmParameters($arguments[$i]);
			}
		}

		$result = call_user_func_array([static::getEntity()->getDataClass(), $name], $arguments);

		// Merge orm params for query method call
		if ($name === 'query' && $result instanceof ORM\Query\Query) {
			foreach (static::mergeOrmParameters() as $key => $parameter) {
				switch ($key) {
					case 'filter':
						foreach ($parameter as $field => $value) {
							$result->where($field, $value);
						}
						break;

					case 'runtime':
						foreach ($parameter as $runtimeName => $field) {
							$result->registerRuntimeField(
								is_string($runtimeName) ? $runtimeName : $field->getName(),
								$field
							);
						}
						break;
				}
			}
		}

		// Add runtime fields from mergeOrmParameters to getMap result
		if ($name === 'getMap') {
			$parameters = static::mergeOrmParameters();
			if (is_array($parameters['runtime'])) {
				foreach ($parameters['runtime'] as $runtimeName => $field) {
					$result[$runtimeName] = $field;
				}
			}
		}

		// Tag cache for querying requests
		if ($result instanceof ORM\Query\Result || $name === 'getCount') {
			if (defined('BX_COMP_MANAGED_CACHE') && is_object($GLOBALS['CACHE_MANAGER'])) {
				$GLOBALS['CACHE_MANAGER']->RegisterTag(static::class);
			}
		}

		return $result;
	}
}
