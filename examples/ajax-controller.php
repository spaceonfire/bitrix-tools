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

use Bitrix\Main\Context;
use spaceonfire\BitrixTools\Controllers\BaseController;

try {
    $requestUri = Context::getCurrent()->getServer()->getRequestUri();
    $requestUri = substr(rtrim($requestUri, '/\\'), 6); // '/ajax/' is 6 symbols long

    $urlParts = explode('/', $requestUri);

    [$controllerName, $actionName] = $urlParts + [null, null];
    $paramPairs = array_slice($urlParts, 2);

    $controller = BaseController::factory($controllerName ?: 'default', 'Vendor\Module\Controllers');

    $controller->setParamsPairs($paramPairs);

    $controller->doAction($actionName ?: 'default');
} catch (Throwable $e) {
    echo $e->GetMessage();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php';
