<?php

namespace spaceonfire\BitrixTools\Controllers;

use CSaleBasket;
use spaceonfire\BitrixTools\Common;
use spaceonfire\BitrixTools\Views\JsonView;
use Throwable;

try {
    Common::loadModules(['sale']);
} catch (Throwable $throwable) {
    return;
}

/**
 * Контроллер корзины
 */
class BasketController extends BaseController
{
    /**
     * Удаляет товар из корзины
     */
    public function removeAction(): void
    {
        $this->view = new JsonView();
        $this->returnAsIs = true;
        $id = $this->getParam('ID');
        CSaleBasket::Delete($id);
    }

    /**
     * Изменение количества покупаемого товара
     */
    public function updateCountAction(): void
    {
        $this->view = new JsonView();
        $this->returnAsIs = true;
        $id = $this->getParam('ID');
        $quantity = $this->getParam('QUANTITY');
        CSaleBasket::Update($id, ['QUANTITY' => $quantity]);
    }
}
