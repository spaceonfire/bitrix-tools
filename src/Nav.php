<?php

namespace spaceonfire\BitrixTools;

class Nav
{
	/**
	 * Normalize Bitrix menu nav to multidimensional array
	 * @param array $nav Bitrix menu nav array
	 * @return array normalized menu
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

		$children = function (&$item, &$list) use (&$children) {
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

		foreach ($nav as $i => $arItem) {
			$children($nav[$i], $nav);
		}
		$nav = array_filter($nav);
		return array_values($nav);
	}
}
