<?php

namespace spaceonfire\BitrixTools;

abstract class Nav
{
    final private function __construct()
    {
    }

    /**
     * Преобразует стандартный плоский массив навигационного меню, сгенерированный компонентом bitrix:menu,
     * в многоуровневый вложенный массив
     * @param array $nav Массив навигационного меню из компонента bitrix:menu
     * @return array Преобразованный многоуровневый массив
     */
    public static function normalizeMenuNav(array $nav): array
    {
        foreach ($nav as $key => $arItem) {
            if ($arItem['DEPTH_LEVEL'] > 1) {
                for ($i = $key - 1; $i >= 0; $i--) {
                    if ($nav[$i]['DEPTH_LEVEL'] < $arItem['DEPTH_LEVEL']) {
                        $nav[$i]['CHILDREN'][] = $key;
                        break;
                    }
                }
            }
        }

        $children = static function (&$item, &$list) use (&$children): void {
            if (!empty($item['CHILDREN'])) {
                foreach ($item['CHILDREN'] as $key => $id) {
                    $childItem = $list[$id];
                    if (!empty($childItem['CHILDREN'])) {
                        $children($childItem, $list);
                    }
                    $item['CHILDREN'][$key] = $childItem;
                    unset($list[$id]);
                }
            }
        };

        foreach ($nav as $i => $_) {
            $children($nav[$i], $nav);
        }
        $nav = array_filter($nav);
        return array_values($nav);
    }

    /**
     * Проверяет есть ли у пользователя доступ к $path
     * @param string $path Путь к файлу или папке относительно корня
     * @return bool
     */
    public static function isUserHasAccessToFile(string $path): bool
    {
        global $APPLICATION;
        return $APPLICATION->GetFileAccessPermission($path) !== 'D';
    }
}
