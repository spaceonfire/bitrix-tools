<?php

namespace spaceonfire\BitrixTools\ORM;

use Bitrix\Main\Entity\DataManager;

class IblockPropMultiple extends DataManager
{
	/**
	 * @abstract
	 * @return int
	 * @throws \Bitrix\Main\NotImplementedException
	 */
	public static function getIblockId(): int
	{
		throw new \Bitrix\Main\NotImplementedException('Method getIblockId() must be implemented by successor.');
	}

	public static function getTableName(): string
	{
		return 'b_iblock_element_prop_m' . static::getIblockId();
	}

	public static function getMap(): array
	{
		return [
			'ID' => [
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true,
			],
			'IBLOCK_ELEMENT_ID' => [
				'data_type' => 'integer',
				'primary' => true,
			],
			'IBLOCK_PROPERTY_ID' => [
				'data_type' => 'integer',
			],
			'VALUE' => [
				'data_type' => 'string',
			],
			'DESCRIPTION' => [
				'data_type' => 'string',
			],
			'VALUE_NUM' => [
				'data_type' => 'float',
			],
		];
	}
} 
