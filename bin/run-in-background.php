<?php
// Defines for cli call
define('NOT_CHECK_PERMISSIONS', true);
define('NO_AGENT_STATISTIC', true);
define('STOP_STATISTICS', true);
define('BX_CRONTAB_SUPPORT', true);
define('BX_BUFFER_USED', true);
define('BX_NO_ACCELERATOR_RESET', true);
set_time_limit(0);
ini_set('memory_limit', '2048M');
error_reporting(E_ERROR | E_STRICT);

// Parse options
$arScriptArgs = getopt('', ['options:']);

/**
 * @var array $options
 *      $options = [
 *          'func' => (callable) PHP function. Must be callable defined as string or array
 *          'args' => (array) An array of arguments to pass to func
 *          'modules' => (array) An array of modules to load
 *          'components' => (array) An array of components to load their classes
 *          'server' => (array) rewrite $_SERVER global var fields
 *          'userId' => (int) authorize as userId
 *      ]
 */
$options = json_decode($arScriptArgs['options'], true);

list(
	'func' => $func,
	'args' => $arguments,
	'modules' => $modules,
	'components' => $components,
	'server' => $server,
	'userId' => $userId,
) = $options;

foreach ($server as $key => $value) {
	$_SERVER[$key] = $value;
}

if (empty($_SERVER['DOCUMENT_ROOT'])) {
	// TODO: properly find document root
	$_SERVER['DOCUMENT_ROOT'] = __DIR__ . '/../../../..';
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

global $USER;
if ($userId > 0) {
	$USER->Authorize($userId);
}

foreach ($modules as $module) {
	\Bitrix\Main\Loader::includeModule($module);
}

foreach ($components as $component) {
	CBitrixComponent::includeComponentClass($component);
}

call_user_func_array($func, $arguments);
