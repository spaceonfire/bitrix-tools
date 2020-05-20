<?php

namespace spaceonfire\BitrixTools\ORM;

use Bitrix\Iblock\IblockSiteTable;
use Bitrix\Iblock\SectionTable;
use Bitrix\Main\ORM\Query\Filter\ConditionTree;
use RuntimeException;
use Throwable;

abstract class IblockSection extends SectionTable
{
    /**
     * Возвращает ID инфоблока. Необходимо переопределять метод.
     * @return int
     */
    abstract public static function getIblockId(): int;

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
     * Возвращает схему полей сущности
     * @return array
     */
    public static function getMap(): array
    {
        $map = parent::getMap();
        $map['PARENT_SECTION'] = [
            'data_type' => static::class,
            'reference' => ['=this.IBLOCK_SECTION_ID' => 'ref.ID'],
        ];

        $map = array_merge($map, static::getUrlTemplateMap($map));

        return $map;
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
                    'DETAIL_PAGE_URL' => 'IBLOCK.SECTION_PAGE_URL',
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
