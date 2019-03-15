<?php

namespace spaceonfire\BitrixTools\Mvc\Controller;

use spaceonfire\BitrixTools\Common;
use spaceonfire\BitrixTools\Mvc;

try {
	Common::loadModules(['sale']);
} catch (\Throwable $throwable) {
	return;
}

/**
 * Контроллер корзины
 */
class Basket extends Prototype
{
	/**
	 * Удаляет товар из корзины
	 *
	 * @return void
	 */
	public function removeAction(): void
	{
		$this->view = new Mvc\View\Json();
		$this->returnAsIs = true;
		$id = $this->getParam("ID");
		\CSaleBasket::Delete($id);
	}

	/**
	 * Изменение количества покупаемого товара
	 *
	 */
	public function updateCountAction(): void
	{
		$this->view = new Mvc\View\Json();
		$this->returnAsIs = true;
		$id = $this->getParam('ID');
		$quantity = $this->getParam('QUANTITY');
		\CSaleBasket::Update($id, ['QUANTITY' => $quantity]);
	}
}
