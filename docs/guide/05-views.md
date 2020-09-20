# Виды (Views)

Виды (View) - это часть архитектуры MVC, которая отвечает за показ данных конечному пользователю.
Такой подход позволяет разделить рендеринг данных в шаблон от логики обработки запроса.
В библиотеке `spaceonfire/bitrix-tools` представлены несколько классов видов.

## PhpView

PhpView пожалуй самый часто используемый тип видов, который позволяет рендерить переданные данные в PHP скрипт-шаблон.

Для того чтобы им воспользоваться, при создании объекта вида и передаем необходимые параметры:

-   Имя файла шаблона
-   Данные для использования в шаблоне
-   Директория в которой искать файл шаблона

```php
use spaceonfire\BitrixTools\Views\PhpView;

$phpView = new PhpView(
    'my-view-template.php',
    ['foo' => 'bar'],
    '/path/to/project/views'
);
```

Данные вида доступны в шаблоне в переменной `$result`. Важно понимать, что в этой переменной могут быть разные данные
и это не обязательно должен быть массив. Так же в самом шаблоне можно использовать класс `Bitrix\Main\Localization\Loc`
для локализации текстов, соответствующий файл переводов будет загружен автоматически.

```php
<?php

/**
 * @var array $result
 * @var spaceonfire\BitrixTools\Views\PhpView $this
 */

use Bitrix\Main\Localization\Loc;

?>
<p><?= Loc::getMessage('foo_label') ?>: <?= $this->escape($result['foo']) ?></p>
```

Рендеринг вида производится вызовом метода `render()`. Так же рендеринг вызывается приведением объекта вида к строке:

```php
/** @var spaceonfire\BitrixTools\Views\PhpView $phpView */

// Рендеринг вызовом методота
$renderByMethod = $phpView->render();

// Рендеринг приведением к строке
$renderByCast = (string)$phpView;

// Рендеринг приведением к строке при выводе на экран
echo $phpView;
```

## HtmlView

HtmlView служит для того, чтобы рендерить переданные в виде HTML строк данные:

```php
use spaceonfire\BitrixTools\Views\HtmlView;

$htmlView = new HtmlView('<p>hello</p>');
// $htmlView->render() === '<p>hello</p>';

$htmlView2 = new HtmlView(['<p>html1</p>', '<p>html2</p>']);
// $htmlView2->render() === '<p>html1</p><p>html2</p>';
```

## JsonView и XmlView

JsonView и XmlView обычно используются в ajax/rest эндпоинтах, для вывода некоторой структуры данных в соответствующем
формате.

```php
use spaceonfire\BitrixTools\Views\JsonView;
use spaceonfire\BitrixTools\Views\XmlView;

$data = [
    'id' => 42,
    'foo' => 'bar',
];

$jsonView = new JsonView($data);
$xmlView = new XmlView($data);
```
