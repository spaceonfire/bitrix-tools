<?php

namespace spaceonfire\BitrixTools;

use Bitrix\Main;

class Php
{
	/**
	 * Запускает фоновый процесс PHP для выполнения некоторой функции.
	 *
	 * Принимает в качетсве аргумента `$options` массив со следующими ключами:
	 *
	 * ```php
	 * $options = [
	 *     'func' => (callable) PHP функция для выполнения в фоне. Необходимо передавать callable в виде строки или массива
	 *     'args' => (array) Массив аргументов для передачи в функцию
	 *     'modules' => (array) Массив модулей 1С-Битрикс, которые необходимо загрузить для корректного выполнения функции
	 *     'components' => (array) Массив компонентов 1С-Битрикс, для подключения их классов
	 *     'server' => (array) Ассоциативный массив, который переназначит поля глобальной переменной $_SERVER
	 *     'userId' => (int) ID пользователя, под которым необходимо авторизоваться
	 * ]
	 * ```
	 *
	 * @param array $options
	 * @return string|null shell_exec result
	 * @throws Main\ArgumentTypeException
	 */
	public static function runInBackground(array $options): ?string
	{
		// Prepare options
		$options = array_merge([
			'func' => null,
			'args' => [],
			'modules' => [],
			'components' => [],
			'server' => [],
			'userId' => null,
		], $options);

		if (
			(
				!is_array($options['func']) &&
				!is_string($options['func'])
			) || !is_callable($options['func'])
		) {
			throw new Main\ArgumentTypeException('$options[func]', 'callable');
		}

		$binPath = dirname(__DIR__) . '/bin/run-in-background.php';
		$logPath = dirname(__DIR__) . '/bin/run-in-background.log';

		$command = implode(' ', [
			'php',
			$binPath,
			'--options',
			escapeshellarg(json_encode($options)),
			'>>',
			$logPath,
			'&',
		]);

		return shell_exec($command);
	}
}
