<?php

namespace spaceonfire\BitrixTools\Mvc\Controller;

use Bitrix\Main;
use spaceonfire\BitrixTools\Mvc;

/**
 * Прототип MVC контроллера
 */
class Prototype
{
	/**
	 * Request
	 *
	 * @var Main\Context\HttpRequest
	 */
	protected $request;

	/**
	 * View
	 *
	 * @var Mvc\View\Prototype|null
	 */
	protected $view;

	/**
	 * Вернуть возвращенные экшеном данные как есть, без признака success
	 *
	 * @var boolean
	 */
	protected $returnAsIs = false;

	/**
	 * Параметры
	 *
	 * @var array
	 */
	protected $params = [];

	/**
	 * Создает новый контроллер
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->request = Main\Context::getCurrent()->getRequest();
	}

	/**
	 * "Фабрика" контроллеров
	 *
	 * @param string $name Имя сущности
	 * @param string $namespace Неймспейс класса
	 * @return Prototype
	 * @throws \Exception
	 */
	public static function factory($name, $namespace = __NAMESPACE__): Prototype
	{
		$name = preg_replace('/[^A-z0-9_]/', '', $name);
		$namespace = rtrim($namespace, '\\');
		$className = $namespace . '\\' . ucfirst($name);

		if (!class_exists($className)) {
			throw new \Exception(sprintf('Controller "%s" doesn\'t exists.', $name));
		}

		return new $className();
	}

	/**
	 * Выполняет экшн контроллера
	 *
	 * @param string $name Имя экшена
	 * @return void
	 * @throws \Exception
	 */
	public function doAction($name): void
	{
		$name = preg_replace('/[^A-z0-9_]/', '', $name);
		$methodName = $name . 'Action';

		if (!method_exists($this, $methodName)) {
			throw new \Exception(sprintf('Action "%s" doesn\'t exists.', $name));
		}

		//JSON view by default
		$this->view = new Mvc\View\Json();

		$response = new \stdClass();
		$response->success = false;
		try {
			$response->data = $this->$methodName();
			$response->success = true;
		} catch (\Exception $e) {
			$response->code = $e->getCode();
			$response->message = $e->getMessage();
		}

		try {
			$this->view->setData($this->returnAsIs ? ($response->data ?? null) : $response);
			$this->view->sendHeaders();
			print $this->view->render();
		} catch (\Exception $e) {
			print $e->getMessage();
		}
	}

	/**
	 * Возвращает код, сгенерированный компонентом Bitrix
	 *
	 * @param string $name Имя компонента
	 * @param string $template Шаблон компонента
	 * @param array $params Параметры компонента
	 * @param mixed $componentResult Данные, возвращаемые компонентом
	 * @return string
	 */
	protected function getComponent($name, $template = '', $params = [], &$componentResult = null): string
	{
		ob_start();
		$componentResult = $GLOBALS['APPLICATION']->IncludeComponent($name, $template, $params);
		$result = ob_get_contents();
		ob_end_clean();

		return $result;
	}

	/**
	 * Возвращает код, сгенерированный включаемой областью Bitrix
	 *
	 * @param string $path Путь до включаемой области
	 * @param array $params Массив параметров для подключаемого файла
	 * @param array $function_params Массив настроек данного метода
	 * @return string
	 */
	protected function getIncludeArea($path, $params = [], $function_params = []): string
	{
		ob_start();
		$GLOBALS['APPLICATION']->IncludeFile($path, $params, $function_params);
		$result = ob_get_contents();
		ob_end_clean();
		return $result;
	}

	/**
	 * Устанавливает параметры из пар в массиве
	 *
	 * @param array $pairs Пары [ключ][значение]
	 * @return void
	 */
	public function setParamsPairs($pairs): void
	{
		foreach ($pairs as $name) {
			$value = next($pairs) === false ? null : current($pairs);
			$this->params[$name] = $value;
		}
	}

	/**
	 * Возвращает значение входного параметра
	 *
	 * @param string $name Имя параметра
	 * @param mixed $default Значение по умолчанию
	 * @return mixed
	 */
	protected function getParam($name, $default = '')
	{
		$result = array_key_exists($name, $this->params)
			? $this->params[$name]
			: $this->request->get($name);

		return $result ?? $default;
	}
}
