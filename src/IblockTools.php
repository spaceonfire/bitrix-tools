<?php

namespace spaceonfire\BitrixTools;

use Bitrix\Iblock;
use Bitrix\Iblock\InheritedProperty\ElementValues;
use Bitrix\Iblock\InheritedProperty\SectionValues;
use Bitrix\Iblock\SectionTable;
use Bitrix\Main;
use Bitrix\Main\ORM\Objectify\EntityObject;
use CIblock;
use CIBlockProperty;
use CIBlockPropertyEnum;
use spaceonfire\BitrixTools\CacheMap\IblockCacheMap;
use Webmozart\Assert\Assert;

abstract class IblockTools
{
    final private function __construct()
    {
    }

    /**
     * Возвращает ID инфоблока по символьному коду
     *
     * @param string $code
     * @return null|int
     */
    public static function getIblockIdByCode(string $code): ?int
    {
        $id = IblockCacheMap::getId($code);
        return $id ? (int)$id : null;
    }

    /**
     * Собирает схему инфоблока, состоящую из полей элемента инфоблока и его свойств.
     *
     * Принимает в качестве аргумента `$options` массив со следующими ключами:
     *
     * ```php
     * $options = [
     *     'IBLOCK_ID' => (int) ID инфоблока
     *     'DEFAULT_FIELDS' => (array) Массив полей по-умолчанию, известных заранее
     *     'EXCLUDE_FIELDS' => (array) Массив полей, которые необходимо исключить из итоговой схемы
     * ]
     * ```
     *
     * @param array $options
     * @return array Схема инфоблока - массив ассоциативных массивов, описывающих поля и свойства инфоблока
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
                if ('' . $id !== $field->getName()) {
                    $id = $field->getName();
                }

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
            static function ($nIblockId, $arDefaultFields) {
                $arProps = [];
                $arPropertySchema = [];

                $rsPropsQ = CIBlock::GetProperties($nIblockId, [], ['ACTIVE' => 'Y']);
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
                            $rsPropertyEnum = CIBlockProperty::GetPropertyEnum($arProp['ID']);
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
                                'select' => ['ID', 'NAME'],
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
                                'select' => ['ID', 'NAME'],
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
            [$options['IBLOCK_ID'], $options['DEFAULT_FIELDS']]
        );

        $arPropertySchema = $arCacheData['arPropertySchema'];

        $arSchema = array_merge($arSchema, $arPropertySchema);

        $arExcluded = $options['EXCLUDE_FIELDS'];
        $arSchema = array_filter($arSchema, static function ($arField) use ($arExcluded) {
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
        while (CIblock::isEnabledClearTagCache()) {
            CIblock::disableClearTagCache();
        }
        return true;
    }

    /**
     * Включает сброс тэгированного кэша инфоблока
     * @return bool
     */
    public static function enableIblockCacheClear(): bool
    {
        while (!CIblock::isEnabledClearTagCache()) {
            CIblock::enableClearTagCache();
        }
        return true;
    }

    /**
     * Возвращает список разделов инфоблока, выравненные по вложенности точками
     * @param int $iblockId ID инфоблока
     * @param array $parameters дополнительные параметры запроса
     * @return array Массив вида `[SECTION_ID => SECTION_NAME]`
     */
    public static function getSectionsTree(int $iblockId, array $parameters = []): array
    {
        Common::loadModules(['iblock']);

        $parameters = ArrayTools::merge([
            'filter' => [
                'IBLOCK_ID' => $iblockId,
                'ACTIVE' => 'Y',
            ],
            'select' => ['ID', 'NAME', 'DEPTH_LEVEL'],
            'order' => ['LEFT_MARGIN' => 'ASC'],
        ], $parameters);

        $cacheParams = [
            'CACHE_ID' => substr(md5(serialize([__METHOD__, $iblockId, $parameters])), 0, 10),
            'CACHE_TAG' => 'iblock_id_' . $iblockId,
            'CACHE_PATH' => implode(DIRECTORY_SEPARATOR, ['', __METHOD__]),
        ];

        return Cache::cacheResult($cacheParams, static function ($parameters) {
            $sections = SectionTable::getList($parameters)->fetchCollection();

            $ret = [];
            /** @var EntityObject $section */
            foreach ($sections as $section) {
                $name = $section->get('NAME');
                if ($section->get('DEPTH_LEVEL') > 1) {
                    $name = str_repeat(' . ', $section->get('DEPTH_LEVEL')) . $name;
                }
                $ret[$section->getId()] = $name;
            }

            return $ret;
        }, [$parameters]);
    }

    /**
     * Возвращает список свойств для инфоблока
     * @param int $iblockId ID инфоблока
     * @return array
     */
    public static function getProperties(int $iblockId): array
    {
        $cacheOptions = [
            'CACHE_ID' => md5(static::class . '::' . __FUNCTION__),
            'CACHE_PATH' => '/iblock_tools/',
            'CACHE_TIME' => 36000,
            'CACHE_TAG' => 'property_iblock_id_' . $iblockId,
        ];

        return Cache::cacheResult($cacheOptions, static function (int $iblockId) {
            $propertiesQuery = CIBlockProperty::GetList([], [
                'IBLOCK_ID' => $iblockId,
            ]);
            $properties = [];
            while ($property = $propertiesQuery->Fetch()) {
                if (empty($property['CODE'])) {
                    continue;
                }
                $properties[$property['CODE']] = $property;
            }
            return $properties;
        }, [$iblockId]);
    }

    /**
     * Возвращает символьный код свойства по его ID
     * @param int $iblockId ID инфоблока
     * @param int $id ID свойства
     * @return string|null
     */
    public static function getPropertyCodeById(int $iblockId, int $id): ?string
    {
        foreach (static::getProperties($iblockId) as $code => $property) {
            if ((int)$property['ID'] === $id) {
                return $code;
            }
        }

        return null;
    }

    /**
     * Возвращает ID свойства по его коду
     * @param int $iblockId ID инфоблока
     * @param string $code Символьный код свойства
     * @return int|null
     */
    public static function getPropertyIdByCode(int $iblockId, string $code): ?int
    {
        $arProperty = static::getProperties($iblockId);
        return $arProperty[$code]['ID'];
    }


    /**
     * Возвращает значения всех свойств типа "список"
     * @param int|null $iblockId ID инфоблока. Если передан `null`, будут возвращены все свойства, сгруппированные по
     *     инфоблокам
     * @return array
     */
    public static function getEnums(?int $iblockId = null): array
    {
        $cacheOptions = [
            'CACHE_ID' => md5(static::class . '::' . __FUNCTION__),
            'CACHE_PATH' => '/iblock_tools/',
            'CACHE_TIME' => 36000,
        ];

        $enums = Cache::cacheResult($cacheOptions, static function () {
            $propertiesQuery = CIBlockProperty::getList(['ID' => 'ASC'], [
                'ACTIVE' => 'Y',
                'PROPERTY_TYPE' => 'L',
            ]);

            $propertyToIblockMap = [];

            $enums = [];

            while ($arProperty = $propertiesQuery->Fetch()) {
                $enums[$arProperty['IBLOCK_ID']][$arProperty['CODE']] = [];
                $propertyToIblockMap[$arProperty['ID']] = $arProperty['IBLOCK_ID'];
            }

            $enumQuery = CIBlockPropertyEnum::getList(['ID' => 'ASC']);
            while ($enumValue = $enumQuery->Fetch()) {
                $iblockId = $propertyToIblockMap[$enumValue['PROPERTY_ID']];
                $enums[$iblockId][$enumValue['PROPERTY_CODE']][$enumValue['XML_ID']] = [
                    'ID' => (int)$enumValue['ID'],
                    'XML_ID' => $enumValue['XML_ID'],
                    'VALUE' => $enumValue['VALUE'],
                    'IS_DEFAULT' => $enumValue['DEF'] === 'Y',
                ];
            }

            return $enums;
        });

        if ($iblockId === null) {
            return $enums;
        }

        return $enums[$iblockId] ?? [];
    }

    /**
     * Возвращает значение enum свойства по id
     * @param int $iblockId ID инфоблока
     * @param int|null $id ID значения. Если передан `null`, будет возвращено значение по-умолчанию
     * @param string $propertyCode Символьный код свойства
     * @return string|null
     */
    public static function getEnumValueById(int $iblockId, ?int $id, string $propertyCode): ?string
    {
        $enums = static::getEnums($iblockId);

        foreach ($enums[$propertyCode] as $enum) {
            if ($id === (int)$enum['ID'] || ($id === null && $enum['IS_DEFAULT'])) {
                return $enum['VALUE'];
            }
        }

        return null;
    }

    /**
     * Возвращает значение enum свойства по его xml id
     * @param int $iblockId ID инфоблока
     * @param string|null $xml XML_ID значения. Если передан `null`, будет возвращено значение
     *     по-умолчанию
     * @param string $propertyCode Символьный код свойства
     * @return string|null
     */
    public static function getEnumValueByXmlId(int $iblockId, ?string $xml, string $propertyCode): ?string
    {
        $id = static::getEnumIdByXmlId($iblockId, $xml, $propertyCode);
        return static::getEnumValueById($iblockId, $id, $propertyCode);
    }

    /**
     * Возвращает id значения enum свойства по XML_ID
     * @param int $iblockId ID инфоблока
     * @param string $xml - XML_ID значения. Если передан `null`, будет возвращено значение
     *     по-умолчанию
     * @param string $propertyCode - Символьный код свойства
     * @return int|null
     */
    public static function getEnumIdByXmlId(int $iblockId, ?string $xml, string $propertyCode): ?int
    {
        $enums = static::getEnums($iblockId);

        if ($xml === null) {
            foreach ($enums[$propertyCode] as $enum) {
                if ($enum['IS_DEFAULT']) {
                    return $enum['ID'];
                }
            }

            return null;
        }

        Assert::notEmpty($xml);

        return !empty($enums[$propertyCode][$xml]) ? $enums[$propertyCode][$xml]['ID'] : null;
    }

    /**
     * Возвращает xml_id значения enum свойства по id
     * @param int $iblockId ID инфоблока
     * @param int|null $id ID значения. Если передан `null`, будет возвращено значение по-умолчанию
     * @param string $propertyCode Символьный код свойства
     * @return string|null
     */
    public static function getEnumXmlIdById(int $iblockId, ?int $id, string $propertyCode): ?string
    {
        $enums = static::getEnums($iblockId);

        foreach ($enums[$propertyCode] as $xmlId => $arEnumValue) {
            if ($id === (int)$arEnumValue['ID'] || ($id === null && $arEnumValue['IS_DEFAULT'])) {
                return (string)$xmlId;
            }
        }

        return null;
    }

    /**
     * Возвращает SEO мета-данные для элемента инфоблока по ID
     * @param int $iblockId ID инфоблока
     * @param int $elementId ID элемента
     * @return array
     */
    public static function getElementMeta(int $iblockId, int $elementId): array
    {
        return (new ElementValues($iblockId, $elementId))->getValues();
    }

    /**
     * Возвращает SEO мета-данные для раздела инфоблока по ID
     * @param int $iblockId ID инфоблока
     * @param int $sectionId ID раздела
     * @return array
     */
    public static function getSectionMeta(int $iblockId, int $sectionId): array
    {
        return (new SectionValues($iblockId, $sectionId))->getValues();
    }
}
