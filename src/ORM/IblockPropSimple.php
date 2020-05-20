<?php

namespace spaceonfire\BitrixTools\ORM;

use Bitrix\Main\ORM\Data\DataManager;
use spaceonfire\BitrixTools\IblockTools;

abstract class IblockPropSimple extends DataManager
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
        return 'b_iblock_element_prop_s' . static::getIblockId();
    }

    /**
     * Возвращает схему полей сущности
     * @return array
     */
    public static function getMap(): array
    {
        $map = [
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
        $map = array_merge($map, self::getPropertyMap());
        return $map;
    }

    private static function getPropertyMap(): array
    {
        $propertyMap = [];

        $properties = IblockTools::getProperties(static::getIblockId());

        foreach ($properties as $property) {
            if ($property['MULTIPLE'] === 'Y') {
                continue;
            }

            $fieldOptions = [
                'expression' => ['%s', 'PROPERTY_' . $property['ID'],],
            ];
            switch ($property['PROPERTY_TYPE']) {
                case 'L':
                case 'F':
                case 'G':
                case 'E':
                case 'S:UserID':
                case 'E:EList':
                case 'S:FileMan':
                    $fieldOptions['data_type'] = 'integer';
                    break;

                case 'S:DateTime':
                    $fieldOptions['data_type'] = 'datetime';
                    break;

                case 'N':
                    $fieldOptions['data_type'] = 'float';
                    break;
                case 'S':
                default:
                    $fieldOptions['data_type'] = 'string';

                    if ($property['USER_TYPE'] === 'HTML') {
                        $fieldOptions['data_type'] = 'text';
                        $fieldOptions['serialized'] = true;
                    }

                    break;
            }

            $propertyMap[$property['CODE']] = $fieldOptions;
            $propertyMap['PROPERTY_' . $property['ID']] = [
                'data_type' => $fieldOptions['data_type']
            ];
        }

        return $propertyMap;
    }
}
