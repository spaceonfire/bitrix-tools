<?php

namespace spaceonfire\BitrixTools\ORM;

use Bitrix\Iblock;
use Bitrix\Iblock\IblockSiteTable;
use Bitrix\Main\DB\SqlExpression;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Query\Filter\ConditionTree;
use RuntimeException;
use spaceonfire\BitrixTools\IblockTools;
use Throwable;

abstract class IblockElement extends DataManager
{
    /**
     * Возвращает ID инфоблока
     *
     * Если Вам заранее известен ID инфоблока, лучше самостоятельно возвращать его в переопределении
     * метода. Иначе следует переопределить метод `getIblockCode()`.
     *
     * @abstract
     * @return int
     */
    public static function getIblockId(): int
    {
        if (static::getIblockCode() === '') {
            throw new RuntimeException('Method getIblockCode() returned an empty string');
        }

        $iblockId = IblockTools::getIblockIdByCode(static::getIblockCode());

        if (!$iblockId) {
            throw new RuntimeException('Iblock id cannot be found by code');
        }

        return $iblockId;
    }

    /**
     * Возвращает символьный код инфоблока.
     * @abstract
     * @return string
     */
    public static function getIblockCode(): string
    {
        throw new RuntimeException('Method getIblockCode() must be implemented by successor.');
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
     * Возвращает схему полей сущности
     * @return array
     */
    public static function getMap(): array
    {
        $map = Iblock\ElementTable::getMap();

        $propertySimpleClassName = str_replace('Table', '', static::class) . 'PropSimpleTable';
        if (class_exists($propertySimpleClassName)) {
            $map['PROPERTY_SIMPLE'] = [
                'data_type' => $propertySimpleClassName,
                'reference' => [
                    '=this.ID' => 'ref.IBLOCK_ELEMENT_ID',
                ],
            ];
        }

        $sectionClassName = str_replace('Table', '', static::class) . 'SectionTable';
        if (class_exists($sectionClassName)) {
            $map['SECTION'] = [
                'data_type' => $sectionClassName,
                'reference' => [
                    '=this.IBLOCK_SECTION_ID' => 'ref.ID',
                ],
            ];

            $map['SECTION_CODE'] = [
                'data_type' => 'string',
                'expression' => ['%s', 'SECTION.CODE'],
            ];

            $map['SECTION_ID'] = [
                'data_type' => 'string',
                'expression' => ['%s', 'IBLOCK_SECTION_ID'],
            ];

            $map['SECTION_ELEMENT'] = [
                'data_type' => SectionElementTable::class,
                'reference' => [
                    '=this.ID' => 'ref.IBLOCK_ELEMENT_ID',
                ],
            ];

            $map['SECTIONS'] = [
                'data_type' => $sectionClassName,
                'reference' => [
                    '=this.SECTION_ELEMENT.IBLOCK_SECTION_ID' => 'ref.ID',
                ],
            ];
        }

        $map = array_merge($map, static::getPropertyMultipleMap());
        $map = array_merge($map, static::getUrlTemplateMap($map));

        return $map;
    }

    protected static function getPropertyMultipleMap(): array
    {
        $propertiesMap = [];

        $propertyMultipleClassName = str_replace('Table', '', static::class) . 'PropMultipleTable';

        if (class_exists($propertyMultipleClassName)) {
            $properties = IblockTools::getProperties(static::getIblockId());

            foreach ($properties as $code => $property) {
                if ($property['MULTIPLE'] === 'Y') {
                    $propertiesMap['PROPERTY_MULTIPLE_' . $code] = [
                        'data_type' => $propertyMultipleClassName,
                        'reference' => [
                            '=this.ID' => 'ref.IBLOCK_ELEMENT_ID',
                            'ref.IBLOCK_PROPERTY_ID' => new SqlExpression('?i', $property['ID']),
                        ],
                    ];
                }
            }
        }

        return $propertiesMap;
    }

    /**
     * @inheritDoc
     */
    public static function getList(array $parameters = [])
    {
        if (!isset($parameters['filter'])) {
            $parameters['filter'] = new ConditionTree();
        }

        if ($parameters['filter'] instanceof ConditionTree) {
            $oldFilter = $parameters['filter'];
            $parameters['filter'] = (new ConditionTree())->where('IBLOCK_ID', static::getIblockId());
            if ($oldFilter->hasConditions()) {
                $parameters['filter']->where($oldFilter);
            }
        } else {
            // I should trigger notice that array filter may cause an unexpected behavior
            $parameters['filter']['IBLOCK_ID'] = static::getIblockId();
        }

        return parent::getList($parameters);
    }

    /**
     * Возвращает значение enum свойства по id
     *
     * @param int|null $id ID значения, если null будет возвращено значение по-умолчанию
     * @param string $propertyCode Символьный код свойства
     * @return string|null
     */
    public static function getEnumValueById(?int $id, string $propertyCode): ?string
    {
        return IblockTools::getEnumValueById(static::getIblockId(), $id, $propertyCode);
    }

    /**
     * Возвращает id значения enum свойства по XML_ID
     *
     * @param string $xml - xml_id property value
     * @param string $propertyCode - Character property code
     * @return int|null
     */
    public static function getEnumIdByXmlId($xml, $propertyCode): ?int
    {
        return IblockTools::getEnumIdByXmlId(static::getIblockId(), $xml, $propertyCode);
    }

    /**
     * Возвращает xml_id значения enum свойства по id
     *
     * @param int|null $id ID значения, если null будет возвращено значение по-умолчанию
     * @param string $propertyCode Символьный код свойства
     * @return string|int|null
     */
    public static function getXmlIdById(?int $id, string $propertyCode)
    {
        return IblockTools::getEnumXmlIdById(static::getIblockId(), $id, $propertyCode);
    }

    /**
     * @return array|mixed
     * @deprecated use IblockTools::getProperties
     */
    protected static function getProperties()
    {
        return IblockTools::getProperties(static::getIblockId());
    }

    /**
     * Возвращает символьный код свойства по его ID
     *
     * @param int $id ID свойства
     * @return null|string
     */
    public static function getPropertyCodeById(int $id): ?string
    {
        return IblockTools::getPropertyCodeById(static::getIblockId(), $id);
    }

    /**
     * Возвращает ID свойства по его коду
     *
     * @param string $code Символьный код свойства
     * @return null|int
     */
    public static function getPropertyIdByCode(string $code): ?int
    {
        return IblockTools::getPropertyIdByCode(static::getIblockId(), $code);
    }

    /**
     * Возвращает Expression поле для получения URL детальной страницы
     *
     * @param array $modelMap - текущая схема полей сущности
     * @return array
     */
    private static function getUrlTemplateMap(array $modelMap = []): array
    {
        $urlTemplateMap = [];

        try {
            $iblockInfo = IblockSiteTable::getRow([
                'select' => [
                    'DETAIL_PAGE_URL' => 'IBLOCK.DETAIL_PAGE_URL',
                    'SITE_ID',
                    'DIR' => 'SITE.DIR',
                    'SERVER_NAME' => 'SITE.DIR',
                ],
                'filter' => [
                    'IBLOCK_ID' => static::getIblockId(),
                ],
                'cache' => [
                    'ttl' => 36000,
                    'cache_joins' => true,
                ],
            ]);
        } catch (Throwable $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        $isAdminPage = ((defined('ADMIN_SECTION') && ADMIN_SECTION === true) || !defined('BX_STARTED'));

        if ($iblockInfo !== null) {
            if (!$isAdminPage && defined('SITE_DIR') && defined('SITE_SERVER_NAME')) {
                $replacements = [SITE_DIR, SITE_SERVER_NAME];
            } else {
                $replacements = [$iblockInfo['DIR'], $iblockInfo['SERVER_NAME']];
            }

            $templateUrl = str_replace(['#SITE_DIR#', '#SERVER_NAME#'], $replacements, $iblockInfo['DETAIL_PAGE_URL']);

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
            $urlTemplateMap['DETAIL_PAGE_URL'] = [
                'data_type' => 'string',
                'expression' => $expressionFields
            ];
        }

        return $urlTemplateMap;
    }
}
