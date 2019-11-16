<?php

namespace spaceonfire\BitrixTools;

use Bitrix\Iblock;
use Bitrix\Main;
use spaceonfire\BitrixTools\CacheMap\IblockCacheMap;

class IblockTools
{
	/**
	 * Возвращает ID инфоблока по символьному коду
	 *
	 * @param string $code
	 * @return null|int
	 */
	public static function getIblockIdByCode(string $code): ?int
	{
		return (int)IblockCacheMap::getId($code);
	}

	/**
	 * Собирает схему инфоблока, состаящую из полей элемента инфоблока и его свойств.
	 *
	 * Принимает в качетсве аргумента `$options` массив со следующими ключами:
	 *
	 * ```php
	 * $options = [
	 *     'IBLOCK_ID' => (int) ID инфоблока
	 *     'DEFAULT_FIELDS' => (array) Массив полей по-умолчанию, известных заранее
	 *     'EXCLUDE_FIELDS' => (array) Массив полей, которые необходимо исключить из итоговой схемы
	 * ]
	 * ```
	 *
	 * @noinspection PhpDocMissingThrowsInspection
	 * @param array $options
	 * @return array Схема инфоблока - массив ассоциативных массивов, описывающих поля инфоблока (тип, название, id поля и пр.)
	 * @throws Main\LoaderException
	 */
	public static function buildSchema($options = []): array
	{
		Common::loadModules(['iblock']);

		$arSchema = [];
		$arMap = Iblock\ElementTable::getMap();
		foreach ($arMap as $id => $field) {
			// Skip for expression field cause getDataType() throws error
			if (
				$field instanceof Main\Entity\ExpressionField ||
				$field instanceof Main\Entity\ReferenceField
			) {
				continue;
			}

			if ($field instanceof Main\ORM\Fields\Field) {
				if ('' . $id !== $field->getName()) $id = $field->getName();

				$arField = [
					'id' => $field->getName(),
					'sort' => $field->getName(),
					'name' => $field->getTitle(),
					'type' => $field->getDataType(),
					'default' => in_array($field->getName(), $options['DEFAULT_FIELDS'], true),
					'items' => is_callable([$field, 'getValues']) ? $field->getValues() : null,
					'isFilter' => true,
				];
			} else {
				$arField = [
					'type' => $field['data_type'],
				];
			}

			// Convert orm data type to filter type
			switch ($arField['type']) {
				case 'float':
				case 'integer':
					$arField['type'] = 'number';
					break;

				case 'datetime':
				case 'date':
					$arField['type'] = 'date';
					break;

				case 'string':
				case 'text':
					$arField['type'] = 'string';
					break;

				case 'enum':
					$arField['type'] = 'list';
					break;

				case 'boolean':
					$arField['type'] = 'checkbox';
					break;
			}

			$arSchema[$id] = $arField;
		}

		// Get iblock properties
		$arCacheData = Cache::cacheResult(
			[
				'CACHE_ID' => substr(md5(serialize([__CLASS__, __FUNCTION__])), 0, 10),
				'CACHE_TAG' => 'iblock_id_' . $options['IBLOCK_ID'],
				'CACHE_PATH' => implode(DIRECTORY_SEPARATOR, ['', __CLASS__, __FUNCTION__]),
			],
			function ($nIblockId, $arDefaultFields) {
				$arProps = [];
				$arPropertySchema = [];

				$rsPropsQ = \CIBlock::GetProperties($nIblockId, [], ['ACTIVE' => 'Y']);
				while ($arProp = $rsPropsQ->Fetch()) {
					$arField = [
						'id' => 'PROPERTY_' . $arProp['CODE'],
						'sort' => 'PROPERTY_' . $arProp['CODE'],
						'name' => $arProp['NAME'],
						'type' => strtolower($arProp['USER_TYPE'] ?: $arProp['PROPERTY_TYPE']),
						'default' => in_array('PROPERTY_' . $arProp['CODE'], $arDefaultFields, true),
						'isFilter' => $arProp['FILTRABLE'] === 'Y',
					];

					switch ($arField['type']) {
						case 'f':
							$arField['type'] = 'file';
							break;
						case 's':
							$arField['type'] = 'string';
							break;
						case 'n':
							$arField['type'] = 'number';
							break;

						case 'l':
							$arField['type'] = 'list';
							$rsPropertyEnum = \CIBlockProperty::GetPropertyEnum($arProp['ID']);
							$arField['items'] = [];
							while ($arPropertyEnum = $rsPropertyEnum->Fetch()) {
								$arField['items'][$arPropertyEnum['ID']] = $arPropertyEnum['VALUE'];
							}
							break;

						case 'e':
							$arField['type'] = 'list';
							$arField['items'] = [];

							$arTmpElements = Iblock\ElementTable::getList([
								'filter' => [
									'IBLOCK_ID' => $arProp['LINK_IBLOCK_ID'],
								],
								'select' => [
									'ID',
									'NAME',
								],
								'limit' => 250,
							])->fetchAll();
							foreach ($arTmpElements as $arElement) {
								$arField['items'][$arElement['ID']] = $arElement['NAME'];
							}
							unset($arTmpElements);
							break;

						case 'g':
							$arField['type'] = 'list';
							$arField['items'] = [];

							$arTmpSections = Iblock\SectionTable::getList([
								'filter' => [
									'IBLOCK_ID' => $arProp['LINK_IBLOCK_ID'],
								],
								'select' => [
									'ID',
									'NAME',
								],
								'limit' => 250,
							])->fetchAll();
							foreach ($arTmpSections as $arSection) {
								$arField['items'][$arSection['ID']] = $arSection['NAME'];
							}
							unset($arTmpSections);
							break;
					}

					$arPropertySchema[$arField['id']] = $arField;

					$arProps['PROPERTY_' . $arProp['CODE']] = $arProp;
				}

				return [
					'arPropertySchema' => $arPropertySchema,
					'arProps' => $arProps,
				];
			},
			[
				$options['IBLOCK_ID'],
				$options['DEFAULT_FIELDS'],
			]
		);

		$arPropertySchema = $arCacheData['arPropertySchema'];

		$arSchema = array_merge($arSchema, $arPropertySchema);

		$arExcluded = $options['EXCLUDE_FIELDS'];
		$arSchema = array_filter($arSchema, function ($arField) use ($arExcluded) {
			return !in_array($arField['id'], $arExcluded, true);
		});

		return [
			'schema' => $arSchema,
			'properties' => $arCacheData['arProps'],
		];
	}

	/**
	 * Отключает сброс тэгированного кэша инфоблока
	 * @return bool
	 */
	public static function disableIblockCacheClear(): bool
	{
		while (\CIblock::isEnabledClearTagCache()) {
			\CIblock::disableClearTagCache();
		}
		return true;
	}

	/**
	 * Включает сброс тэгированного кэша инфоблока
	 * @return bool
	 */
	public static function enableIblockCacheClear(): bool
	{
		while (!\CIblock::isEnabledClearTagCache()) {
			\CIblock::enableClearTagCache();
		}
		return true;
	}
}
