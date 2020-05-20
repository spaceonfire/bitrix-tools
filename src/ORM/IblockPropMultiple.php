<?php

namespace spaceonfire\BitrixTools\ORM;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\NotImplementedException;

class IblockPropMultiple extends DataManager
{
    /**
     * Возвращает ID инфоблока. Необходимо переопределять метод.
     *
     * @noinspection PhpDocMissingThrowsInspection
     * @abstract
     * @return int
     */
    public static function getIblockId(): int
    {
        throw new NotImplementedException('Method getIblockId() must be implemented by successor.');
    }

    /**
     * Возвращает название таблицы для сущности в БД
     *
     * @noinspection PhpDocMissingThrowsInspection
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
