<?php

declare(strict_types=1);

namespace spaceonfire\BitrixTools\CacheMap;

use Bitrix\Iblock\IblockTable;
use Bitrix\Main;
use Bitrix\Main\ORM\Data\DataManager;
use RuntimeException;
use spaceonfire\BitrixTools\Common;
use Throwable;

/**
 * Класс IblockCacheMap позволяет получить информацию об инфоблоке по его символьному коду из кэша
 * @package spaceonfire\BitrixTools\CacheMap
 */
final class IblockCacheMap extends AbstractStaticCacheMap
{
    /**
     * @inheritDoc
     */
    public static function getInstance(): CacheMap
    {
        static $instance;

        if ($instance === null) {
            Common::loadModules(['iblock']);

            try {
                $instance = new QueryCacheMap(
                    IblockTable::query()
                        ->setSelect(['*'])
                        ->setFilter([
                            'ACTIVE' => 'Y',
                            '!CODE' => false,
                        ]),
                    new CacheMapOptions('iblock-cache-map-' . md5(self::class))
                );
            } catch (Main\SystemException $e) {
                throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
            }
        }

        return $instance;
    }

    /**
     * Регистрация обработчиков событий для очистки кэша при изменении сущности
     * Вызывается автоматически при подключении autoloader.
     */
    public static function register(): void
    {
        try {
            Common::loadModules(['iblock']);
        } catch (Throwable $e) {
            return;
        }

        try {
            $eventManager = Main\EventManager::getInstance();

            $ormEntity = IblockTable::getEntity();

            $eventsTree = [
                'iblock' => [
                    'OnAfterIBlockAdd' => 1,
                    'OnAfterIBlockUpdate' => 1,
                    'OnAfterIBlockDelete' => 1,
                    $ormEntity->getNamespace() . $ormEntity->getName() . '::' . DataManager::EVENT_ON_AFTER_ADD => 2,
                    $ormEntity->getNamespace() . $ormEntity->getName() . '::' . DataManager::EVENT_ON_AFTER_UPDATE => 2,
                    $ormEntity->getNamespace() . $ormEntity->getName() . '::' . DataManager::EVENT_ON_AFTER_DELETE => 2,
                ],
            ];

            foreach ($eventsTree as $moduleId => $events) {
                foreach ($events as $event => $version) {
                    if ($version === 2) {
                        $eventManager->addEventHandler($moduleId, $event, [self::class, 'clearCache']);
                    } else {
                        $eventManager->addEventHandlerCompatible($moduleId, $event, [self::class, 'clearCache']);
                    }
                }
            }
        } catch (Main\SystemException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
