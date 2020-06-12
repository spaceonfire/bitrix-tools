<?php

declare(strict_types=1);

namespace spaceonfire\BitrixTools\ORM;

use Bitrix\Highloadblock\DataManager;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\AddResult;
use Bitrix\Main\ORM\Data\UpdateResult;
use Bitrix\Main\ORM\Fields\Relations;
use Bitrix\Main\SystemException;
use RuntimeException;
use spaceonfire\BitrixTools\CacheMap\HighloadBlockCacheMap;
use spaceonfire\BitrixTools\Common;

Common::loadModules(['highloadblock']);
Loc::loadMessages(__FILE__);

/**
 * BaseHighLoadBlockDataManager - базовый класс для создания ORM сущностей HighLoad блоков
 * @package spaceonfire\BitrixTools\ORM
 */
abstract class BaseHighLoadBlockDataManager extends DataManager
{
    protected static $_highloadBlocks = [];

    /**
     * Возвращает ID или NAME HighLoad блока
     * @return int|string
     */
    abstract public static function getHLId();

    /**
     * Возвращает ID HighLoad блока
     * @return int|null
     */
    public static function getRealId(): ?int
    {
        if (($hlId = static::getHLId()) > 0) {
            return (int)$hlId;
        }

        return (int)HighloadBlockCacheMap::getId($hlId);
    }

    /**
     * Возвращает имя таблицы для HighLoad блока
     * @return string|null
     */
    public static function getTableName(): ?string
    {
        $data = static::getHighloadBlock();
        return $data['TABLE_NAME'];
    }

    /**
     * Определяет список полей для сущности
     * @return array
     * @see \Bitrix\Main\ORM\Entity::getFields() Используйте чтобы получить список инициализированных полей
     */
    public static function getMap(): array
    {
        try {
            global $USER_FIELD_MANAGER;

            $map = [];
            $fields = HighloadBlockTable::compileEntity(static::getHighloadBlock())->getFields();

            $hlId = static::getRealId();
            $userFields = $USER_FIELD_MANAGER->GetUserFields('HLBLOCK_' . $hlId, 0, LANGUAGE_ID);

            foreach ($fields as $field) {
                $field->resetEntity();
                $field->configureTitle($userFields[$field->getName()]['LIST_COLUMN_LABEL']);
                $map[$field->getName()] = $field;
            }

            return $map;
        } catch (SystemException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Возвращает данные о HighLoad блоке
     * @return array|null
     */
    public static function getHighloadBlock(): ?array
    {
        $id = static::getRealId();
        if (static::$_highloadBlocks[$id] === null) {
            static::$_highloadBlocks[$id] = HighloadBlockTable::resolveHighloadblock($id);
        }

        return static::$_highloadBlocks[$id];
    }

    protected static function filterData(array $data): array
    {
        if (isset($data['fields']) && is_array($data['fields'])) {
            $filteredData = $data['fields'];
        } else {
            $filteredData = $data;
        }

        unset($filteredData['__object']);

        $entity = static::getEntity();

        foreach ($filteredData as $key => $_) {
            if (!$entity->hasField($key)) {
                unset($filteredData[$key]);
                continue;
            }

            $field = $entity->getField($key);

            if ($field->isPrimary()) {
                unset($filteredData[$key]);
            }

            if ($field instanceof Relations\Reference && !empty($field->getElementals())) {
                unset($filteredData[$key]);
            }
        }

        return $filteredData;
    }

    /**
     * Добавлят новую строку в таблицу HighLoad блока
     * @param array $data
     * @return AddResult
     */
    public static function add(array $data)
    {
        return parent::add(static::filterData($data));
    }

    /**
     * Обновляет строку в таблице HighLoad блока по первичному ключу
     * @param mixed $primary
     * @param array $data
     * @return UpdateResult
     */
    public static function update($primary, array $data)
    {
        return parent::update($primary, static::filterData($data));
    }
}
