<?php

declare(strict_types=1);

namespace spaceonfire\BitrixTools\ORM;

use Bitrix\Highloadblock\DataManager;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Localization\Loc;
use spaceonfire\BitrixTools\Common;

Common::loadModules(['highloadblock']);
Loc::loadMessages(__FILE__);

/**
 * BaseHighLoadBlockDataManager - базовый класс для создания ORM сущностей HighLoad блоков
 * @package spaceonfire\BitrixTools\ORM
 */
abstract class BaseHighLoadBlockDataManager extends DataManager
{
	protected static $_highloadBlocks = [];

	/**
	 * Возвращает ID или NAME HighLoad блока
	 * @return int|string
	 */
	abstract public static function getHLId();

	/**
	 * Возвращает имя таблицы для HighLoad блока
	 * @return string|null
	 */
	public static function getTableName(): ?string
	{
		$data = static::getHighloadBlock();
		return $data['TABLE_NAME'];
	}

	/**
	 * Возвращает ID пользовательских полей для HighLoad блока
	 * @return string|null
	 */
	public static function getUfId()
	{
		$data = static::getHighloadBlock();
		return 'HLBLOCK_' . $data['ID'];
	}

	/**
	 * Определяет список полей для сущности
	 * @return array
	 * @throws \Bitrix\Main\SystemException
	 * @see Используйте \Bitrix\Main\ORM\Entity::getFields() чтобы получить список инициализированных полей
	 */
	public static function getMap(): array
	{
		$fields = HighloadBlockTable::compileEntity(static::getHighloadBlock())->getFields();
		foreach ($fields as $field) {
			$field->resetEntity();
		}
		return $fields;
	}

	/**
	 * Возвращает данные о HighLoad блоке
	 * @return array|null
	 */
	public static function getHighloadBlock(): ?array
	{
		$id = static::getHLId();
		if (static::$_highloadBlocks[$id] === null) {
			static::$_highloadBlocks[$id] = HighloadBlockTable::resolveHighloadblock(static::getHLId());
		}

		return static::$_highloadBlocks[$id];
	}
}
