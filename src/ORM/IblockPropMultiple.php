<?php

namespace spaceonfire\BitrixTools\ORM;

use Bitrix\Main\ORM\Data\DataManager;

abstract class IblockPropMultiple extends DataManager
{
    /**
     * Возвращает ID инфоблока. Необходимо переопределять метод.
     * @return int
     */
    abstract public static function getIblockId(): int;

    /**
     * Возвращает название таблицы для сущности в БД
     * @return string
     */
    public static function getTableName(): string
    {
        return 'b_iblock_element_prop_m' . static::getIblockId();
    }

    /**
     * Возврщает схему полей сущности
     * @return array
     */
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
