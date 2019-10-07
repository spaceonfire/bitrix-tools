<?php

use spaceonfire\BitrixTools\Views;
use spaceonfire\BitrixTools\Controllers;

class_alias(Views\BaseView::class, 'spaceonfire\BitrixTools\Mvc\View\Prototype');
class_alias(Views\HtmlView::class, 'spaceonfire\BitrixTools\Mvc\View\Html');
class_alias(Views\JsonView::class, 'spaceonfire\BitrixTools\Mvc\View\Json');
class_alias(Views\PhpView::class, 'spaceonfire\BitrixTools\Mvc\View\Php');
class_alias(Views\XmlView::class, 'spaceonfire\BitrixTools\Mvc\View\Xml');

class_alias(Controllers\BaseController::class, 'spaceonfire\BitrixTools\Mvc\Controller\Prototype');
class_alias(Controllers\BasketController::class, 'spaceonfire\BitrixTools\Mvc\Controller\Basket');
class_alias(Controllers\FormController::class, 'spaceonfire\BitrixTools\Mvc\Controller\Form');
