<?php

namespace spaceonfire\BitrixTools\ORM;

use Bitrix\Main\ORM\Data\DataManager;
use spaceonfire\BitrixTools\Cache;

/**
 * Class IblockSectionPropSimple
 *
 * If you are using access user fields using @see \Bitrix\Main\ORM\Data\DataManager::getUfId ,
 * you may encounter problem when need to do a join on the value of the property.
 * Bitrix orm generates wrong alias for the join table.
 * To resolve this problem, use this class.
 */
abstract class IblockSectionPropSimple extends DataManager
{
    /**
     * @abstract
     * @return int
     */
    abstract public static function getIblockId(): int;

    public static function getTableName(): string
    {
        return 'b_uts_iblock_' . static::getIblockId() . '_section';
    }

    /**
     * @return string|mixed
     * @deprecated
     */
    public static function getFilePath()
    {
        return __FILE__;
    }

    public static function getMap(): array
    {
        $map = [
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

        $cacheOptions = [
            'CACHE_ID' => md5(static::class . '::' . __FUNCTION__),
            'CACHE_PATH' => '/bitrix_tools/',
            'CACHE_TIME' => 36000,
            'CACHE_TAG' => 'section_ufields_iblock_id_' . static::getIblockId(),
        ];

        $userFields = Cache::cacheResult($cacheOptions, static function (int $iblockId) {
            global $USER_FIELD_MANAGER;
            return $USER_FIELD_MANAGER->GetUserFields('IBLOCK_' . $iblockId . '_SECTION');
        }, [static::getIblockId()]);

        foreach ($userFields as $userField) {
            if ($userField['MULTIPLE'] !== 'N') {
                continue;
            }

            $fieldOption = [];

            switch ($userField['USER_TYPE']['BASE_TYPE']) {
                case 'int':
                case 'enum':
                case 'file':
                    $fieldOption['data_type'] = 'integer';
                    break;

                case 'double':
                    $fieldOption['data_type'] = 'float';
                    break;

                case 'date':
                case 'datetime':
                    $fieldOption['data_type'] = 'datetime';
                    break;

                default:
                    $fieldOption['data_type'] = 'string';
                    break;
            }
            $map[$userField['FIELD_NAME']] = $fieldOption;
        }

        return $map;
    }
}
