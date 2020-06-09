<?php

declare(strict_types=1);

namespace spaceonfire\BitrixTools\CacheMap;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main;
use Bitrix\Main\ORM\Data\DataManager;
use RuntimeException;
use spaceonfire\BitrixTools\Common;
use Throwable;

/**
 * Класс HighloadBlockCacheMap позволяет получить информацию об HighLoad блоке по его названию из кэша
 * @package spaceonfire\BitrixTools\CacheMap
 */
final class HighloadBlockCacheMap extends AbstractStaticCacheMap
{
    /**
     * @inheritDoc
     */
    public static function getInstance(): CacheMap
    {
        static $instance;

        if ($instance === null) {
            Common::loadModules(['highloadblock']);

            try {
                $instance = new QueryCacheMap(
                    HighloadBlockTable::query()
                        ->setSelect(['*'])
                        ->setFilter(['!NAME' => false]),
                    new CacheMapOptions('highload-cache-map-' . md5(self::class), 'ID', 'NAME')
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
            Common::loadModules(['highloadblock']);
        } catch (Throwable $e) {
            return;
        }

        try {
            $eventManager = Main\EventManager::getInstance();

            $ormEntity = HighloadBlockTable::getEntity();

            $eventsTree = [
                $ormEntity->getModule() => [
                    $ormEntity->getNamespace() . $ormEntity->getName() . '::' . DataManager::EVENT_ON_AFTER_ADD,
                    $ormEntity->getNamespace() . $ormEntity->getName() . '::' . DataManager::EVENT_ON_AFTER_UPDATE,
                    $ormEntity->getNamespace() . $ormEntity->getName() . '::' . DataManager::EVENT_ON_AFTER_DELETE,
                ],
            ];

            foreach ($eventsTree as $moduleId => $events) {
                foreach ($events as $event) {
                    $eventManager->addEventHandler($moduleId, $event, [self::class, 'clearCache']);
                }
            }
        } catch (Main\SystemException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
