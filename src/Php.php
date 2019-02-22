<?php

namespace spaceonfire\BitrixTools;

use Bitrix\Main;

class Php
{
	/**
	 * Execute PHP code function
	 * @param array $options
	 *      $options = [
	 *          'func' => (callable) PHP function. Must be callable defined as string or array
	 *          'args' => (array) An array of arguments to pass to func
	 *          'modules' => (array) An array of modules to load
	 *          'components' => (array) An array of components to load their classes
	 *          'server' => (array) rewrite $_SERVER global var fields
	 *          'userId' => (int) authorize as userId
	 *      ]
	 * @return string|null shell_exec result
	 * @throws Main\ArgumentTypeException
	 */
	public static function runInBackground(array $options)
	{
		// Prepare options
		$options = array_merge([
			'func' => null,
			'args' => [],
			'modules' => [],
			'components' => [],
			'server' => [],
		], $options);

		if (
			!is_callable($options['func']) || (
				!is_array($options['func']) &&
				!is_string($options['func'])
			)
		) {
			throw new Main\ArgumentTypeException('$options[func]', 'callable');
		}

		$binPath = realpath(__DIR__ . '/../bin/run-in-background.php');
		$logPath = __DIR__ . '/../bin/run-in-background.log';

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
