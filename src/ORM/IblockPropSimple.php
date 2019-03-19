<?php

namespace spaceonfire\BitrixTools\ORM;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Loader;
use Bitrix\Main\NotImplementedException;

abstract class IblockPropSimple extends DataManager
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
		return 'b_iblock_element_prop_s' . static::getIblockId();
	}

	public static function getMap(): array
	{
		$arMap = [
			'IBLOCK_ELEMENT_ID' => [
				'data_type' => 'integer',
				'primary' => true,
			],
			'IBLOCK_ELEMENT' => [
				'data_type' => str_replace('PropSimple', '', static::class),
				'reference' => [
					'=this.IBLOCK_ELEMENT_ID' => 'ref.ID',
				],
			],
		];
		$arMap = array_merge($arMap, self::getPropertyMap());
		return $arMap;
	}

	private static function getPropertyMap(): array
	{
		global $CACHE_MANAGER;
		$obCache = new \CPHPCache;
		$cacheId = md5(static::class . '::' . __FUNCTION__);
		$arProperties = [];
		if ($obCache->InitCache(36000, $cacheId, '/')) {
			$vars = $obCache->GetVars();

			$arProperties = $vars['arProperties'];
		} elseif (Loader::includeModule('iblock') && $obCache->StartDataCache()) {
			$arFilter = [
				'IBLOCK_ID' => static::getIblockId(),
				'MULTIPLE' => 'N'
			];
			$rsProperty = \CIBlockProperty::GetList(
				[],
				$arFilter
			);
			while ($arProperty = $rsProperty->Fetch()) {
				if (empty($arProperty['CODE'])) {
					continue;
				}

				$arColumn = [
					'expression' => [
						'%s',
						'PROPERTY_' . $arProperty['ID'],
					],
				];
				switch ($arProperty['PROPERTY_TYPE']) {
					case 'L':
					case 'F':
					case 'G':
					case 'E':
					case 'S:UserID':
					case 'E:EList':
					case 'S:FileMan':
						$arColumn['data_type'] = 'integer';
						break;

					case 'S:DateTime':
						$arColumn['data_type'] = 'datetime';
						break;

					case 'N':
						$arColumn['data_type'] = 'float';
						break;
					case 'S':
					default:
						$arColumn['data_type'] = 'string';

						if ($arProperty['USER_TYPE'] === 'HTML') {
							$arColumn['data_type'] = 'text';
							$arColumn['serialized'] = true;
						}

						break;
				}

				$arProperties[$arProperty['CODE']] = $arColumn;
				$arProperties['PROPERTY_' . $arProperty['ID']] = [
					'data_type' => $arColumn['data_type']
				];
			}

			$CACHE_MANAGER->StartTagCache('/');
			$CACHE_MANAGER->RegisterTag('property_iblock_id_' . static::getIblockId());
			$CACHE_MANAGER->EndTagCache();

			$obCache->EndDataCache(['arProperties' => $arProperties]);
		}

		return $arProperties;
	}
}
