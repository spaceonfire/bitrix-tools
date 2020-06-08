<?php

namespace spaceonfire\BitrixTools\CacheMap;

use Bitrix\Main;
use Bitrix\Main\ORM\Data\DataManager;
use RuntimeException;

/**
 * Класс UserGroupCacheMap позволяет получить информацию об группе по ее строковому идентификатора из кэша
 * @package spaceonfire\BitrixTools\CacheMap
 */
final class UserGroupCacheMap implements CacheMapStaticInterface
{
    use CacheMapTrait, CacheMapSingleton;

    /**
     * UserGroupCacheMap constructor.
     */
    private function __construct()
    {
        try {
            $q = Main\GroupTable::query()
                ->setSelect(['*'])
                ->setFilter([
                    'ACTIVE' => 'Y',
                    '!STRING_ID' => false,
                ]);
        } catch (Main\SystemException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        $this->traitConstruct($q, 'ID', 'STRING_ID');
    }

    /**
     * Регистрация обработчиков событий для очистки кэша при изменении сущности.
     * Вызывается автоматически при подключении autoloader.
     */
    public static function register(): void
    {
        try {
            $eventManager = Main\EventManager::getInstance();

            $ormEntity = Main\GroupTable::getEntity();

            $eventsTree = [
                'main' => [
                    'OnAfterGroupAdd' => 1,
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
                        $eventManager->addEventHandler($moduleId, $event, [static::class, 'clearCache']);
                    } else {
                        $eventManager->addEventHandlerCompatible($moduleId, $event, [static::class, 'clearCache']);
                    }
                }
            }
        } catch (Main\SystemException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
