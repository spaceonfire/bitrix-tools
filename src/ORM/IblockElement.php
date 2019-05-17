<?php

namespace spaceonfire\BitrixTools\ORM;

use Bitrix\Main;
use Bitrix\Iblock\IblockSiteTable;
use Bitrix\Iblock\IblockTable;
use Bitrix\Main\DB\SqlExpression;
use Bitrix\Main\Entity\DataManager;

abstract class IblockElement extends DataManager
{
	private static $arEnums = [];

	/**
	 * Возвращает ID инфоблока
	 *
	 * Если Вам заранее известен ID инфоблока, лучше самостоятельно возвращать его в переопределении
	 * метода. Иначе следует переопределить метод `getIblockCode()`.
	 *
	 * @abstract
	 * @return int
	 * @throws Main\NotImplementedException
	 * @throws Main\SystemException
	 */
	public static function getIblockId(): int
	{
		global $CACHE_MANAGER;
		if (strlen(static::getIblockCode()) <= 0) {
			throw new Main\SystemException('Method getIblockCode() returned an null or empty');
		}

		$arIblock = [];
		$obCache = new \CPHPCache;
		$cacheId = md5(static::class . '::' . __FUNCTION__);
		if ($obCache->InitCache(36000, $cacheId, '/')) {
			$vars = $obCache->GetVars();
			$arIblock = $vars['arIblock'];
		} elseif ($obCache->StartDataCache()) {
			$arIblock = IblockTable::getList([
				'select' => ['ID'],
				'filter' => ['=CODE' => static::getIblockCode()],
				'limit' => 1,
			])->fetch();
			if ($arIblock) {
				$CACHE_MANAGER->StartTagCache('/');
				$CACHE_MANAGER->RegisterTag('iblock_id_' . $arIblock['ID']);
				$CACHE_MANAGER->EndTagCache();

				$obCache->EndDataCache(['arIblock' => $arIblock]);
			} else {
				$obCache->AbortDataCache();
			}
		}

		return $arIblock['ID'];
	}

	/**
	 * Возвращает символьный код инфоблока.
	 * @abstract
	 * @return string
	 * @throws Main\NotImplementedException
	 */
	public static function getIblockCode(): string
	{
		throw new Main\NotImplementedException('Method getIblockCode() must be implemented by successor.');
	}

	/**
	 * Возвращает название таблицы для сущности в БД
	 *
	 * @return string
	 */
	public static function getTableName(): string
	{
		return 'b_iblock_element';
	}

	/**
	 * Возврщает схему полей сущности
	 * @return array
	 * @throws Main\ArgumentException
	 * @throws Main\SystemException
	 */
	public static function getMap(): array
	{
		$arMap = [
			'ID' => [
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true,
			],
			'TIMESTAMP_X' => [
				'data_type' => 'datetime',
			],
			'MODIFIED_BY' => [
				'data_type' => 'integer',
			],
			'DATE_CREATE' => [
				'data_type' => 'datetime',
			],
			'CREATED_BY' => [
				'data_type' => 'integer',
			],
			'IBLOCK_ID' => [
				'data_type' => 'integer',
				'required' => true,
			],
			'IBLOCK_SECTION_ID' => [
				'data_type' => 'integer',
			],
			'ACTIVE' => [
				'data_type' => 'boolean',
				'values' => ['N', 'Y'],
			],
			'ACTIVE_FROM' => [
				'data_type' => 'datetime',
			],
			'ACTIVE_TO' => [
				'data_type' => 'datetime',
			],
			'SORT' => [
				'data_type' => 'integer',
			],
			'NAME' => [
				'data_type' => 'string',
				'required' => true,
			],
			'PREVIEW_PICTURE' => [
				'data_type' => 'integer',
			],
			'PREVIEW_TEXT' => [
				'data_type' => 'text',
			],
			'PREVIEW_TEXT_TYPE' => [
				'data_type' => 'enum',
				'values' => ['text', 'html'],
			],
			'DETAIL_PICTURE' => [
				'data_type' => 'integer',
			],
			'DETAIL_TEXT' => [
				'data_type' => 'text',
			],
			'DETAIL_TEXT_TYPE' => [
				'data_type' => 'enum',
				'values' => ['text', 'html'],
			],
			'SEARCHABLE_CONTENT' => [
				'data_type' => 'text',
			],
			'WF_STATUS_ID' => [
				'data_type' => 'integer',
			],
			'WF_PARENT_ELEMENT_ID' => [
				'data_type' => 'integer',
			],
			'WF_NEW' => [
				'data_type' => 'string',
			],
			'WF_LOCKED_BY' => [
				'data_type' => 'integer',
			],
			'WF_DATE_LOCK' => [
				'data_type' => 'datetime',
			],
			'WF_COMMENTS' => [
				'data_type' => 'text',
			],
			'IN_SECTIONS' => [
				'data_type' => 'boolean',
				'values' => ['N', 'Y'],
			],
			'XML_ID' => [
				'data_type' => 'string',
			],
			'CODE' => [
				'data_type' => 'string',
			],
			'ELEMENT_CODE' => [
				'data_type' => 'string',
				'expression' => ['%s', 'CODE']
			],
			'TAGS' => [
				'data_type' => 'string',
			],
			'TMP_ID' => [
				'data_type' => 'string',
			],
			'WF_LAST_HISTORY_ID' => [
				'data_type' => 'integer',
			],
			'SHOW_COUNTER' => [
				'data_type' => 'integer',
			],
			'SHOW_COUNTER_START' => [
				'data_type' => 'datetime',
			],
			'IBLOCK' => [
				'data_type' => IblockTable::class,
				'reference' => ['=this.IBLOCK_ID' => 'ref.ID'],
			],
			'WF_PARENT_ELEMENT' => [
				'data_type' => static::class,
				'reference' => ['=this.WF_PARENT_ELEMENT_ID' => 'ref.ID'],
			],
		];

		$propertySimpleClassName = str_replace('Table', '', static::class) . 'PropSimpleTable';
		if (class_exists($propertySimpleClassName)) {
			$arMap['PROPERTY_SIMPLE'] = [
				'data_type' => $propertySimpleClassName,
				'reference' => [
					'=this.ID' => 'ref.IBLOCK_ELEMENT_ID',
				],
			];
		}

		$sectionClassName = str_replace('Table', '', static::class) . 'SectionTable';
		if (class_exists($sectionClassName)) {
			$arMap['SECTION'] = [
				'data_type' => $sectionClassName,
				'reference' => [
					'=this.IBLOCK_SECTION_ID' => 'ref.ID',
				],
			];

			$arMap['SECTION_CODE'] = [
				'data_type' => 'string',
				'expression' => ['%s', 'SECTION.CODE'],
			];

			$arMap['SECTION_ID'] = [
				'data_type' => 'string',
				'expression' => ['%s', 'IBLOCK_SECTION_ID'],
			];

			$arMap['SECTION_ELEMENT'] = [
				'data_type' => SectionElementTable::class,
				'reference' => [
					'=this.ID' => 'ref.IBLOCK_ELEMENT_ID',
				],
			];

			$arMap['SECTIONS'] = [
				'data_type' => $sectionClassName,
				'reference' => [
					'=this.SECTION_ELEMENT.IBLOCK_SECTION_ID' => 'ref.ID',
				],
			];
		}

		$arMap = array_merge($arMap, static::getPropertyMultipleMap());
		$arMap = array_merge($arMap, static::getUrlTemplateMap($arMap));

		return $arMap;
	}

	protected static function getPropertyMultipleMap()
	{
		global $CACHE_MANAGER;
		$arProperties = [];
		$propertyMultipleClassName = str_replace('Table', '', static::class) . 'PropMultipleTable';
		if (class_exists($propertyMultipleClassName)) {
			$obCache = new \CPHPCache;
			$cacheId = md5(static::class . '::' . __FUNCTION__);
			if ($obCache->InitCache(36000, $cacheId, '/')) {
				$vars = $obCache->GetVars();
				$arProperties = $vars['arProperties'];
			} elseif ($obCache->StartDataCache()) {
				$rsProperty = \CIBlockProperty::GetList(
					[],
					[
						'IBLOCK_ID' => static::getIblockId(),
						'MULTIPLE' => 'Y',
					]
				);
				while ($arProperty = $rsProperty->Fetch()) {
					if (empty($arProperty['CODE'])) {
						continue;
					}

					$arProperties['PROPERTY_MULTIPLE_' . $arProperty['CODE']] = [
						'data_type' => $propertyMultipleClassName,
						'reference' => [
							'=this.ID' => 'ref.IBLOCK_ELEMENT_ID',
							'ref.IBLOCK_PROPERTY_ID' => new SqlExpression('?i', $arProperty['ID']),
						],
					];
				}

				$CACHE_MANAGER->StartTagCache('/');
				$CACHE_MANAGER->RegisterTag('property_iblock_id_' . static::getIblockId());
				$CACHE_MANAGER->EndTagCache();
				$obCache->EndDataCache(['arProperties' => $arProperties]);
			}
		}

		return $arProperties;
	}

	/**
	 * @inheritDoc
	 */
	public static function getList(array $parameters = [])
	{
		$parameters['filter']['IBLOCK_ID'] = static::getIblockId();

		return parent::getList($parameters);
	}

	/**
	 * Возвращает значение enum свойства по id
	 *
	 * @param int|null $id ID значения, если null будет возвращено значение по-умолчанию
	 * @param string $propertyCode Символьный код свойства
	 *
	 * @return string|null
	 * @throws Main\NotImplementedException
	 * @throws Main\SystemException
	 * @throws Main\ArgumentException
	 */
	public static function getEnumValueById(?int $id, string $propertyCode): ?string
	{
		$arProperty = self::getEnums();
		foreach ($arProperty[static::getIblockId()][$propertyCode] as $xmlId => $arEnumValue) {
			if (
				$id === (int)$arEnumValue['ID'] ||
				($id === null && $arEnumValue['IS_DEFAULT'])
			) {
				return $arEnumValue['VALUE'];
			}
		}

		return null;
	}

	/**
	 * Возвращает id значения enum свойства по XML_ID
	 *
	 * @param string $xml - xml_id property value
	 * @param string $propertyCode - Character property code
	 *
	 * @return int|null
	 * @throws Main\NotImplementedException
	 * @throws Main\SystemException
	 * @throws Main\ArgumentException
	 * @throws Main\LoaderException
	 */
	public static function getEnumIdByXmlId($xml, $propertyCode): ?int
	{
		$arProperty = self::getEnums();

		if ($xml && !empty($arProperty[static::getIblockId()][$propertyCode][$xml])) {
			return $arProperty[static::getIblockId()][$propertyCode][$xml]['ID'];
		}

		foreach ($arProperty[static::getIblockId()][$propertyCode] as $xmlId => $arEnumValue) {
			if ($arEnumValue['IS_DEFAULT']) {
				return $xmlId;
			}
		}

		return null;
	}

	/**
	 * Возвращает xml_id значения enum свойства по id
	 *
	 * @param int|null $id ID значения, если null будет возвращено значение по-умолчанию
	 * @param string $propertyCode Символьный код свойства
	 *
	 * @return string|int|null
	 * @throws Main\SystemException
	 * @throws Main\ArgumentException
	 */
	public static function getXmlIdById(?int $id, string $propertyCode)
	{
		$arProperty = self::getEnums();

		foreach ($arProperty[static::getIblockId()][$propertyCode] as $xmlId => $arEnumValue) {
			if (
				$id === (int)$arEnumValue['ID'] ||
				($id === null && $arEnumValue['IS_DEFAULT'])
			) {
				return $xmlId;
			}
		}

		return null;
	}

	private static function getEnums()
	{
		if (!self::$arEnums) {
			self::$arEnums = [];
			$sCacheId = md5(__CLASS__ . '::' . __FUNCTION__);

			$oCache = new \CPHPCache;
			$oCache->InitCache(36000, $sCacheId, '/');
			if (!$arData = $oCache->GetVars()) {
				$oProperties = \CIBlockProperty::getList(
					['ID' => 'ASC'],
					[
						'ACTIVE' => 'Y',
						'PROPERTY_TYPE' => 'L',
					]
				);
				$arProperty2IblockID = [];
				while ($arProperty = $oProperties->Fetch()) {
					self::$arEnums[$arProperty['IBLOCK_ID']][$arProperty['CODE']] = [];
					$arProperty2IblockID[$arProperty['ID']] = $arProperty['IBLOCK_ID'];
				}
				$oEnumProperties = \CIBlockPropertyEnum::getList(['ID' => 'ASC']);
				while ($arEnumProperty = $oEnumProperties->Fetch()) {
					self::$arEnums[$arProperty2IblockID[$arEnumProperty['PROPERTY_ID']]][$arEnumProperty['PROPERTY_CODE']][$arEnumProperty['XML_ID']] = [
						'ID' => (int)$arEnumProperty['ID'],
						'XML_ID' => $arEnumProperty['XML_ID'],
						'VALUE' => $arEnumProperty['VALUE'],
						'IS_DEFAULT' => $arEnumProperty['DEF'] === 'Y',
					];
				}
				if ($oCache->StartDataCache()) {
					$oCache->EndDataCache(['arProperties' => self::$arEnums]);
				}
			} else {
				self::$arEnums = $arData['arProperties'];
			}
		}

		return self::$arEnums;
	}

	protected static function getProperties()
	{
		global $CACHE_MANAGER;
		$obCache = new \CPHPCache;
		$cacheId = md5(static::class . '::' . __FUNCTION__);
		$arProperties = [];
		if ($obCache->InitCache(36000, $cacheId, '/')) {
			$vars = $obCache->GetVars();
			$arProperties = $vars['arProperties'];
		} elseif ($obCache->StartDataCache()) {
			$rsProperty = \CIBlockProperty::GetList([], [
				'IBLOCK_ID' => static::getIblockId(),
			]);
			while ($arProperty = $rsProperty->Fetch()) {
				if (empty($arProperty['CODE'])) {
					continue;
				}
				$arProperties[$arProperty['CODE']] = $arProperty;
			}

			$CACHE_MANAGER->StartTagCache('/');
			$CACHE_MANAGER->RegisterTag('property_iblock_id_' . static::getIblockId());
			$CACHE_MANAGER->EndTagCache();
			$obCache->EndDataCache(['arProperties' => $arProperties]);
		}

		return $arProperties;
	}

	/**
	 * Возвращает символьный код свойства по его ID
	 *
	 * @param int $id ID свойства
	 *
	 * @return null|string
	 */
	public static function getPropertyCodeById(int $id): ?string
	{
		foreach (static::getProperties() as $code => $arProperty) {
			if ((int)$arProperty['ID'] === $id) {
				return $code;
			}
		}

		return null;
	}

	/**
	 * Возвращает ID свойства по его коду
	 *
	 * @param string $code Символьный код свойства
	 *
	 * @return null|int
	 */
	public static function getPropertyIdByCode(string $code): ?int
	{
		$arProperty = static::getProperties();

		return $arProperty[$code]['ID'];
	}

	/**
	 * Возвращает Expression поле для получения URL детальной страницы
	 *
	 * @param array $modelMap - текущая схема полей сущности
	 *
	 * @return array
	 * @throws Main\SystemException
	 * @throws Main\ArgumentException
	 */
	private static function getUrlTemplateMap(array $modelMap = []): array
	{
		global $CACHE_MANAGER;
		$arMap = [];
		$obCache = new \CPHPCache;
		$currentAdminPage = ((defined('ADMIN_SECTION') && ADMIN_SECTION === true) || !defined('BX_STARTED'));
		$cacheId = md5(static::class . '::' . __FUNCTION__ . $currentAdminPage . SITE_ID);

		if ($obCache->InitCache(36000, $cacheId, '/')) {
			$arMap = $obCache->GetVars();
		} elseif ($obCache->StartDataCache()) {
			$obIblock = IblockSiteTable::getList([
				'select' => [
					'DETAIL_PAGE_URL' => 'IBLOCK.DETAIL_PAGE_URL',
					'SITE_ID',
					'DIR' => 'SITE.DIR',
					'SERVER_NAME' => 'SITE.DIR',
				],
				'filter' => [
					'IBLOCK_ID' => static::getIblockId(),
				],
				'limit' => 1,
			]);

			if ($arIblock = $obIblock->fetch()) {
				$templateUrl = $arIblock['DETAIL_PAGE_URL'];

				if ($currentAdminPage) {
					$templateUrl = str_replace(
						['#SITE_DIR#', '#SERVER_NAME#'],
						[$arIblock['DIR'], $arIblock['SERVER_NAME']],
						$templateUrl
					);
				} else {
					$templateUrl = str_replace(
						['#SITE_DIR#', '#SERVER_NAME#'],
						[SITE_DIR, SITE_SERVER_NAME],
						$templateUrl
					);
				}

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
