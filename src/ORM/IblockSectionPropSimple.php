<?php

namespace spaceonfire\BitrixTools\ORM;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\NotImplementedException;

/**
 * Class IblockSectionPropSimple
 *
 * If you are using access user fields using @see \Bitrix\Main\Entity\DataManager::getUfId ,
 * you may encounter problem when need to do a join on the value of the property.
 * Bitrix orm generates wrong alias for the join table.
 * To resolve this problem, use this class.
 */
class IblockSectionPropSimple extends DataManager
{
	/**
	 * @abstract
	 * @return int
	 * @throws \Bitrix\Main\NotImplementedException
	 */
	public static function getIblockId(): int
	{
		throw new NotImplementedException('Method getIblockId() must be implemented by successor.');
	}

	public static function getTableName(): string
	{
		return 'b_uts_iblock_' . static::getIblockId() . '_section';
	}

	public static function getFilePath()
	{
		return __FILE__;
	}

	public static function getMap(): array
	{
		global $USER_FIELD_MANAGER;
		$arMap = [
			'VALUE_ID' => [
				'data_type' => 'integer',
				'primary' => true,
			],
			'SECTION' => [
				'data_type' => str_replace('PropSimple', '', static::class),
				'reference' => [
					'=this.VALUE_ID' => 'ref.ID',
				],
			]
		];
		$obCache = new \CPHPCache;
		$cacheId = md5(static::class . '::' . __FUNCTION__);
		if ($obCache->InitCache(36000, $cacheId, '/' . $cacheId . '/')) {
			$vars = $obCache->GetVars();
			$arMap = $vars['arMap'];
		} elseif ($obCache->StartDataCache()) {
			$arProperties = $USER_FIELD_MANAGER->GetUserFields('IBLOCK_' . static::getIblockId() . '_SECTION');
			foreach ($arProperties as $arProperty) {
				if ($arProperty['MULTIPLE'] !== 'N') {
					continue;
				}
				$arColumn = [];

				switch ($arProperty['USER_TYPE']['BASE_TYPE']) {
					case 'int':
					case 'enum':
					case 'file':
						$arColumn['data_type'] = 'integer';
						break;

					case 'double':
						$arColumn['data_type'] = 'float';
						break;

					case 'date':
					case 'datetime':
						$arColumn['data_type'] = 'datetime';
						break;

					default:
						$arColumn['data_type'] = 'string';
						break;
				}
				$arMap[$arProperty['FIELD_NAME']] = $arColumn;
			}

			$obCache->EndDataCache(['arMap' => $arMap]);
		}

		return $arMap;
	}
}
