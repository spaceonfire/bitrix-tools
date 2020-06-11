<?php
(defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) || die();

// Composer autoloader
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/spaceonfire/bitrix-tools/resources/autoload.php';

define('ROOT_DIR', $_SERVER['DOCUMENT_ROOT']);

try {
    if (false/*env('TRUST_PROXY', false) === true*/) {
        spaceonfire\BitrixTools\Common::trustProxy();
    }

    if (false/*env('DISABLE_LOGIN_BY_HTTP_AUTH', false) === true*/) {
        spaceonfire\BitrixTools\Common::disableHttpAuth();
    }

    /**
     * Set http status header by `CHTTP::SetStatus()` in fast-cgi anyway
     * @see CHTTP::SetStatus()
     */
    if (false/*env('DEFINE_BX_HTTP_STATUS', false) === true*/) {
        define('BX_HTTP_STATUS', true);
    }
} catch (Throwable $exception) {
    Bitrix\Main\Application::getInstance()->getExceptionHandler()->writeToLog($exception);
}
