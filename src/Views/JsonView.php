<?php

namespace spaceonfire\BitrixTools\Views;

use Bitrix\Main\Text\Encoding;

/**
 * JSON MVC view
 */
class JsonView extends BaseView
{
	/**
	 * Создает новый MVC JSON view
	 * @param mixed $data Данные view
	 */
	public function __construct($data = [])
	{
		parent::__construct('', $data);
	}

	/** {@inheritDoc} */
	public function sendHeaders(): void
	{
		header('Content-type: application/json');
	}

	/** {@inheritDoc} */
	public function render(): string
	{
		return json_encode(
			defined('SITE_CHARSET') && SITE_CHARSET !== 'UTF-8' ?
				Encoding::convertEncoding($this->data, 'UTF-8', SITE_CHARSET) :
				$this->data
		);
	}
}
