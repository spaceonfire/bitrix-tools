<?php

namespace spaceonfire\BitrixTools\Controllers;

use CForm;
use InvalidArgumentException;
use Narrowspark\HttpStatus\Exception\NotFoundException;
use spaceonfire\BitrixTools\Common;
use spaceonfire\BitrixTools\Views\HtmlView;
use spaceonfire\BitrixTools\Views\PhpView;
use Throwable;

try {
	Common::loadModules(['form']);
} catch (Throwable $throwable) {
	return;
}

/**
 * Контроллер веб-форм
 */
class FormController extends BaseController
{
	/**
	 * Выводит форму обратной связи
	 * @return string
	 */
	public function feedbackAction(): string
	{
		return $this->getForm('FEEDBACK');
	}

	/**
	 * Выводит форму обратного звонка
	 * @return string
	 */
	public function callbackAction(): string
	{
		return $this->getForm('CALLBACK');
	}

	/**
	 * Выводит форму по параметру в запросе
	 * @return string
	 */
	public function addAction(): string
	{
		return $this->getForm($this->getParam('sid'));
	}

	/**
	 * Выводит компонент добавления результата формы
	 * @param string $sid Символьный код формы
	 * @return string
	 */
	protected function getForm($sid): string
	{
		$this->view = new HtmlView();
		$this->returnAsIs = true;

		$sid = trim($sid);
		if (!$sid) {
			throw new InvalidArgumentException('Form SID is undefined.');
		}

		$form = CForm::GetBySID($sid)->Fetch();
		if (!$form) {
			throw new NotFoundException('The form is not found.');
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
	 * @return array
	 */
	public function resultAction(): array
	{
		$this->view = new PhpView('form/result.php');

		return [
			'result' => $this->getParam('formresult'),
			'resultID' => (int)$this->getParam('RESULT_ID'),
			'formID' => (int)$this->getParam('WEB_FORM_ID'),
		];
	}

	/**
	 * Выводит результаты действий в форме подписки
	 * @return string
	 */
	public function subscribeAction(): string
	{
		$this->view = new HtmlView();
		$this->returnAsIs = true;
		return $this->getComponent('site:subscribtion');
	}
}
