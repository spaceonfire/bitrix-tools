<?php

namespace spaceonfire\BitrixTools\ORM;

use Bitrix\Iblock\IblockTable;
use Bitrix\Iblock\SectionTable;
use Bitrix\Main\NotImplementedException;

class IblockSection extends SectionTable
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

	public static function getList(array $parameters = [])
	{
		$parameters['filter']['IBLOCK_ID'] = static::getIblockId();
		return parent::getList($parameters);
	}

	public static function getMap(): array
	{
		$arMap = parent::getMap();
		$arMap['PARENT_SECTION'] = [
			'data_type' => static::class,
			'reference' => ['=this.IBLOCK_SECTION_ID' => 'ref.ID'],
		];

		$arMap = array_merge($arMap, static::getUrlTemplateMap($arMap));

		return $arMap;
	}


	/**
	 * Returns expression to refer to the detailed page
	 *
	 * @param array $modelMap - current map
	 *
	 * @return array
	 */
	private static function getUrlTemplateMap(array $modelMap = []): array
	{
		global $CACHE_MANAGER;
		$arMap = [];
		$obCache = new \CPHPCache;
		$cacheId = md5(static::class . '::' . __FUNCTION__);

		if ($obCache->InitCache(36000, $cacheId, '/')) {
			$arMap = $obCache->GetVars();
		} elseif ($obCache->StartDataCache()) {
			$obIblock = IblockTable::getList([
				'select' => [
					'LIST_PAGE_URL',
					'SECTION_PAGE_URL'
				],
				'filter' => [
					'ID' => static::getIblockId()
				]
			]);

			if ($arIblock = $obIblock->fetch()) {
				$templateUrl = $arIblock['SECTION_PAGE_URL'];
				$expressionFields = [];
				preg_match_all('/#([^#]+)#/u', $templateUrl, $match);
				if (!empty($match[1])) {
					foreach ($match[1] as $kid => $fieldName) {
						if (array_key_exists($fieldName, $modelMap)) {
							$templateUrl = str_replace($match[0][$kid], '\', %s,\'', $templateUrl);
							$expressionFields[] = $fieldName;
						}
					}
				}

				array_unshift($expressionFields, 'CONCAT(\'' . $templateUrl . '\')');
				$arMap['DETAIL_PAGE_URL'] = [
					'data_type' => 'string',
					'expression' => $expressionFields
				];
			}

			$CACHE_MANAGER->StartTagCache('/');
			$CACHE_MANAGER->RegisterTag('iblock_id_' . static::getIblockId());
			$CACHE_MANAGER->EndTagCache();
			$obCache->EndDataCache($arMap);
		}

		return $arMap;
	}
}
