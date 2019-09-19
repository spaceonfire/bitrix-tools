<?php

declare(strict_types=1);

namespace spaceonfire\BitrixTools\ORM;

use Bitrix\Highloadblock\DataManager;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Fields\IntegerField;
use spaceonfire\BitrixTools\Common;

Common::loadModules(['highloadblock']);
Loc::loadMessages(__FILE__);

/**
 * BaseHighLoadBlockDataManager - базовый класс для создания ORM сущностей HighLoad блоков
 * @package spaceonfire\BitrixTools\ORM
 */
abstract class BaseHighLoadBlockDataManager extends DataManager
{
	protected static $_highloadBlock;

	/**
	 * @return int|string
	 */
	abstract public static function getHLId();

	/** {@inheritdoc} */
	public static function getTableName(): ?string
	{
		$data = static::getHighloadBlock();
		return $data['TABLE_NAME'];
	}

	/**
	 * Returns entity map definition.
	 * @return array
	 * @throws \Bitrix\Main\SystemException
	 * @see \Bitrix\Main\ORM\Entity::getFields() to get initialized fields
	 * @see \Bitrix\Main\ORM\Base::getField() to get initialized fields
	 */
	public static function getMap(): array
	{
		return [
			(new IntegerField('ID'))
				->configurePrimary(true)
				->configureAutocomplete(true),
		];
	}

	/**
	 * Returns data of highload block
	 * @return array|null
	 */
	public static function getHighloadBlock(): ?array
	{
		if (static::$_highloadBlock === null) {
			static::$_highloadBlock = HighloadBlockTable::resolveHighloadblock(static::getHLId());
		}

		return static::$_highloadBlock;
	}
}
