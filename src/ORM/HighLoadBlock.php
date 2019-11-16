<?php

namespace spaceonfire\BitrixTools\ORM;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class HighLoadBlock
 * @deprecated используйте BaseHighLoadBlockDataManager напрямую. Будет удален к 1.0
 * @see BaseHighLoadBlockDataManager
 */
abstract class HighLoadBlock extends BaseHighLoadBlockDataManager
{
	/**
	 * Расширяет параметры запроса
	 *
	 * Метод устарел! Ранее был необходим, т.к. наследники данного класса являлись просто прокси
	 * к скомпилированной сущности HighLoad блока, и не было возможность просто перегрузить
	 * методы типа `getMap()`, `getList()`.
	 * С помощью этого метода расширялись поля `runtime` и другие параметры запроса.
	 *
	 * @param array $parameters
	 * @return array
	 * @deprecated перегружайте методы getList, getMap ... в классах наследниках
	 */
	public static function mergeOrmParameters(array $parameters = [])
	{
		return $parameters;
	}

	/** {@inheritdoc} */
	public static function getList(array $parameters = array())
	{
		return parent::getList(static::mergeOrmParameters($parameters));
	}

	/** {@inheritdoc} */
	public static function getByPrimary($primary, array $parameters = array())
	{
		return parent::getByPrimary($primary, static::mergeOrmParameters($parameters));
	}

	/** {@inheritdoc} */
	public static function query()
	{
		$result = parent::query();

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

		return $result;
	}

	/** {@inheritdoc} */
	public static function getMap(): array
	{
		$result = parent::getMap();

		// Add runtime fields from mergeOrmParameters to getMap result
		$parameters = static::mergeOrmParameters();
		if (is_array($parameters['runtime'])) {
			foreach ($parameters['runtime'] as $runtimeName => $field) {
				if (is_numeric($runtimeName)) {
					$result[] = $field;
				} else {
					$result[$runtimeName] = $field;
				}
			}
		}

		return $result;
	}
}
