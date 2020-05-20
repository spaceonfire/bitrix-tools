<?php

declare(strict_types=1);

namespace spaceonfire\BitrixTools;

use Bitrix\Main\Application;
use Bitrix\Main\Db\SqlQueryException;
use Bitrix\Main\ORM\Entity;
use Bitrix\Main\ORM\Objectify\EntityObject;
use Bitrix\Main\Result;
use RuntimeException;
use stdClass;
use Throwable;
use Webmozart\Assert\Assert;

abstract class ORMTools
{
    final private function __construct()
    {
    }

    public static function collectValuesFromEntityObject(EntityObject $object): object
    {
        $item = new stdClass();

        foreach ($object->collectValues() as $key => $value) {
            if ($value instanceof EntityObject) {
                $value = self::collectValuesFromEntityObject($value);
            }

            $item->{$key} = $value;
        }

        return $item;
    }

    /**
     * Wraps provided callback with transaction
     * @param callable $callback
     * @param array $arguments
     * @return mixed
     * @throws SqlQueryException
     * @throws Throwable
     */
    public static function wrapTransaction(callable $callback, ...$arguments)
    {
        $connection = Application::getConnection();

        try {
            $connection->startTransaction();

            $result = $callback(...$arguments);

            $isSuccess = true;

            if ($result instanceof Result) {
                $isSuccess = $result->isSuccess();
            } elseif (is_bool($result)) {
                $isSuccess = $result;
            }

            if ($isSuccess) {
                $connection->commitTransaction();
            } else {
                $connection->rollbackTransaction();
            }

            return $result;
        } catch (Throwable $e) {
            $connection->rollbackTransaction();
            throw $e;
        }
    }

    /**
     * Extracts primary for provided entity and data
     * @param Entity $entity
     * @param mixed $data
     * @return array
     */
    public static function extractPrimary(Entity $entity, $data): array
    {
        Assert::isArrayAccessible($data);

        $primary = [];

        foreach ($entity->getPrimaryArray() as $field) {
            if (empty($data[$field])) {
                throw new RuntimeException('$item[' . $field . '] is required');
            }

            $primary[$field] = $data[$field];
        }

        return $primary;
    }
}
