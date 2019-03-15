<?php

namespace spaceonfire\BitrixTools\Mvc\Controller;

use Bitrix\Main;
use spaceonfire\BitrixTools\Mvc;

/**
 * Контроллер веб-форм
 */
class Form extends Prototype
{
	/**
	 * Выводит форму обратной связи
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function feedbackAction(): string
	{
		return $this->getForm('FEEDBACK');
	}

	/**
	 * Выводит форму обратного звонка
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function callbackAction(): string
	{
		return $this->getForm('CALLBACK');
	}

	/**
	 * Выводит форму по параметру в запросе
	 *
	 * @return string
	 * @throws \Exception
	 */
	private function addAction(): string
	{
		return $this->getForm($this->getParam('sid'));
	}

	/**
	 * Выводит компонент добавления результата формы
	 *
	 * @param int|string $sid Символьный код формы
	 * @return string
	 * @throws Main\LoaderException
	 */
	protected function getForm($sid): string
	{
		$this->view = new Mvc\View\Html();
		$this->returnAsIs = true;

		$sid = trim($sid);
		if (!$sid) {
			throw new \Exception('Form SID is undefined.');
		}

		Main\Loader::includeModule('form');
		$form = \CForm::GetBySID($sid)->Fetch();
		if (!$form) {
			throw new \Exception('The form is not found.');
		}

		return $this->getComponent('bitrix:form.result.new', '.default', [
			'WEB_FORM_ID' => $form['ID'],
			'IGNORE_CUSTOM_TEMPLATE' => 'N',
			'USE_EXTENDED_ERRORS' => 'Y',
			'SEF_MODE' => 'N',
			'SEF_FOLDER' => '/',
			'CACHE_TYPE' => 'A',
			'CACHE_TIME' => '3600',
			'LIST_URL' => '',
			'EDIT_URL' => '',
			'SUCCESS_URL' => '',
			'CHAIN_ITEM_TEXT' => '',
			'CHAIN_ITEM_LINK' => '',
			'HIDE_TITLE' => 'N',
			'POPUP_MODE' => 'Y',
			'VARIABLE_ALIASES' => [
				'WEB_FORM_ID' => 'WEB_FORM_ID',
				'RESULT_ID' => 'RESULT_ID',
			],
		]);
	}

	/**
	 * Выводит результат заполнения формы
	 *
	 * @return array
	 */
	public function resultAction(): array
	{
		$this->view = new Mvc\View\Php('form/result.php');

		return [
			'result' => $this->getParam('formresult'),
			'resultID' => (int)$this->getParam('RESULT_ID'),
			'formID' => (int)$this->getParam('WEB_FORM_ID'),
		];
	}

	/**
	 * Выводит результаты действий в форме подписки
	 *
	 * @return string
	 */
	protected function subscribeAction(): string
	{
		$this->view = new Mvc\View\Html();
		$this->returnAsIs = true;

		return $this->getComponent('site:subscribtion');
	}
}
