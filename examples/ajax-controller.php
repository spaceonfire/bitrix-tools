<?php
/**
 * Реализует вызов экшена контроллера по шаблону URL
 *
 * 1. Скопируйте файл в /ajax/{module}/.
 *    Файл будет обслуживать запросы по шаблону /ajax/{module}/{controller}/{action}/
 * 2. Замените имя модуля и имя неймспейса с классами контроллеров на Ваши
 * 3. Настройте обработку запросов ^/ajax/{module}/ в urlrewrite.php
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

try {
	if (!\Bitrix\Main\Loader::includeModule('site.main')) {
		throw new \Exception('Can\'t include module "".');
	}

	$urlParts = explode('/', $_SERVER['REQUEST_URI']);
	array_shift($urlParts);
	array_shift($urlParts);
	$controller = spaceonfire\BitrixTools\Mvc\Controller\Prototype::factory(
		array_shift($urlParts) ?: 'default',
		'Vendor\Mvc\Namespace'
	);

	$action = array_shift($urlParts) ?: 'default';
	$controller->setParamsPairs($urlParts);
	$controller->doAction($action);
} catch(Exception $e) {
	print $e->getMessage();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php';
