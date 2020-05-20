<?php

namespace spaceonfire\BitrixTools;

use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\Data\Cache as BxCache;

abstract class Cache
{
    final private function __construct()
    {
    }

    private static function prepareOptions(array $options): array
    {
        $options = array_merge([
            'CACHE_ID' => null,
            'CACHE_TAG' => null,
            'CACHE_PATH' => null,
            'CACHE_TIME' => 36000000,
        ], $options);

        foreach (['CACHE_ID', 'CACHE_PATH'] as $sParam) {
            if (empty($options[$sParam])) {
                throw new ArgumentNullException($sParam);
            }
        }

        $ds = DIRECTORY_SEPARATOR;
        if (!empty($options['CACHE_TAG'])) {
            $options['CACHE_PATH'] .= $ds . $options['CACHE_TAG'];
        }
        $siteId = defined('ADMIN_SECTION') && ADMIN_SECTION ? 's1' : SITE_ID;
        $options['CACHE_PATH'] = str_replace($ds . $ds, $ds, $ds . $siteId . $ds . $options['CACHE_PATH']);

        return $options;
    }

    /**
     * Кэширует результаты выполнения функции `$callback`.
     *
     * Принимает в качетсве аргумента `$options` массив со следующими ключами:
     *
     * ```php
     * $options = [
     *     'CACHE_ID' => (string) ID кэша (обязательный параметр)
     *     'CACHE_PATH' => (string) Относительный путь для сохранения кэша (обязательный параметр). Будет автоматически
     * добавлен ID сайта и CACHE_TAG, если указан
     *     'CACHE_TAG' => (string | array) Включает использование тегированного кэша с переданным тэгом/тэгами
     *     'CACHE_TIME' => (int) Время жизни кэша (TTL) в секундах, по-умолчанию 36000000
     * ]
     * ```
     *
     * @param array $options Массив с параметрами для кэширования
     * @param callable $callback Функция, выполнение которой необходимо кэшировать
     * @param array $args Массив аргументов для функции `$callback`
     * @return mixed Данные возвращаемые функцией `$callback` из кэша
     * @throws ArgumentNullException
     */
    public static function cacheResult(array $options, callable $callback, $args = [])
    {
        $options = static::prepareOptions($options);

        if (!empty($args)) {
            $options['CACHE_ID'] .= ':' . substr(md5(serialize($args)), 0, 10);
        }

        $obCache = BxCache::createInstance();
        $result = null;

        if ($obCache->initCache($options['CACHE_TIME'], $options['CACHE_ID'], $options['CACHE_PATH'])) {
            $result = $obCache->getVars();
        } elseif ($obCache->startDataCache()) {
            $result = call_user_func_array($callback, $args);

            if (!empty($result)) {
                if (!is_array($options['CACHE_TAG'])) {
                    $options['CACHE_TAG'] = [$options['CACHE_TAG']];
                }
                $options['CACHE_TAG'] = array_filter($options['CACHE_TAG']);

                if (defined('BX_COMP_MANAGED_CACHE') && !empty($options['CACHE_TAG'])) {
                    global $CACHE_MANAGER;
                    $CACHE_MANAGER->StartTagCache($options['CACHE_PATH']);
                    foreach ($options['CACHE_TAG'] as $tag) {
                        $CACHE_MANAGER->RegisterTag($tag);
                    }
                    $CACHE_MANAGER->EndTagCache();
                }

                $obCache->endDataCache($result);
            } else {
                $obCache->abortDataCache();
            }
        }

        return $result;
    }

    /**
     * Удаляет кэш
     *
     * @param array $options Массив с параметрами кэширования, как в Cache::cacheResult
     * @throws ArgumentNullException
     * @see Cache::cacheResult
     */
    public static function clearCache(array $options): void
    {
        $options = static::prepareOptions($options);
        BxCache::createInstance()->cleanDir($options['CACHE_PATH']);
    }
}
