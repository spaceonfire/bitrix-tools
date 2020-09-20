# Контроллеры

Контроллеры - это часть архитектуры MVC, которая отвечает за обработку запроса и генерирование ответа.
Библиотека `spaceonfire/bitrix-tools` предоставляет базовый класс контроллера `spaceonfire\BitrixTools\Controllers\BaseController`
для обработки ajax запросов.

Контроллеры состоят из одного или нескольких действий, к которым может обращаться конечный пользователь и
запрашивать исполнение того или иного функционала. Действия это публичные методы контроллера,
в названии которых есть суффикс `Action`.

В качестве примера создадим контроллер для обработки лайков/дизлайков постов:

```php
namespace Vendor\Module\Controllers;

use spaceonfire\BitrixTools\Controllers\BaseController;

final class PostController extends BaseController
{
    public function likeAction()
    {
        // TODO: handle post like action
    }

    public function dislikeAction()
    {
        // TODO: handle post dislike action
    }
}
```

Теперь создадим входную точку, которая будет запускать наши контроллеры, например `ajax/index.php`:

```php
<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

use Bitrix\Main\Context;
use spaceonfire\BitrixTools\Controllers\BaseController;

try {
    $requestUri = Context::getCurrent()->getServer()->getRequestUri();
    $requestUri = substr(rtrim($requestUri, '/\\'), 6); // '/ajax/' is 6 symbols long

    $urlParts = explode('/', $requestUri);

    [$controllerName, $actionName] = $urlParts + [null, null];
    $paramPairs = array_slice($urlParts, 2);

    $controller = BaseController::factory($controllerName ?: 'default', 'Vendor\Module\Controllers');

    $controller->setParamsPairs($paramPairs);

    $controller->doAction($actionName ?: 'default');
} catch (Throwable $e) {
    echo $e->GetMessage();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php';
```

Данный скрипт позволяет направлять обработку запросов по адресам `/ajax/{controller}/{action}[/paramName1/paramValue1]`
на наши контроллеры. Например, для запроса к действию `like` нашего контроллера `PostController` урл будет следующим:
`/ajax/post/like`. Метод `BaseController::factory` ищет класс контроллера по переданному названию в указанном неймспейсе
по прямому совпадению имени класса или с суффиксом `Controller` (рекомендуется именовать контроллеры с суффиксом).

Остается направить запросы на созданную входную точку. Для этого в `urlrewrite.php` надо добавить правило:

```php
[
    'CONDITION' => '#^/ajax/#',
    'RULE' => '',
    'ID' => '',
    'PATH' => '/ajax/index.php',
    'SORT' => '30',
],
```

## Как писать действия

Во-первых, при описании действия внутри контроллера всегда есть доступ к объекту запроса `Bitrix\Main\HttpRequest`,
к которому можно обратиться через свойство `request`.

Контроллеры так же поддерживают передачу параметров (см. метод `setParamsPairs`). Для получение значения параметра
используйте метод `getParam($paramName)`, а для проверки наличия параметра - метод `hasParam($paramName)`.
Если параметр не был установлен напрямую, контроллер попробует найти его в объекте запроса `request`.

Обработка действий (метод `doAction`) завязана на использование [видов (views)][link-docs-views].
По-умолчанию используется вид `JsonView`, результат выполнения действия оборачивается в свойство `data`
и добавляется свойство `success=true`.

Например такая реализация действия like

```php
namespace Vendor\Module\Controllers;

use spaceonfire\BitrixTools\Controllers\BaseController;

final class PostController extends BaseController
{
    public function likeAction()
    {
        return [
            'postId' => 42,
            'userId' => 24,
        ];
    }
}
```

В ответе HTTP вернет следующий JSON:

```json
{
    "success": true,
    "data": {
        "postId": 42,
        "userId": 24
    }
}
```

Чтобы напрямую отдать результат выполнения действия (без оборачивания в свойство `data`) в методе этого действия можно
установить свойство `returnAsIs` в значение `true`:

```php
namespace Vendor\Module\Controllers;

use spaceonfire\BitrixTools\Controllers\BaseController;

final class PostController extends BaseController
{
    public function likeAction()
    {
        $this->returnAsIs = true;

        return [
            'postId' => 42,
            'userId' => 24,
        ];
    }
}
```

Также можно установить свой вид и вернуть данные необходимые для его рендеринга:

```php
namespace Vendor\Module\Controllers;

use spaceonfire\BitrixTools\Controllers\BaseController;
use spaceonfire\BitrixTools\Views\PhpView;

final class PostController extends BaseController
{
    public function likeAction()
    {
        $this->view = new PhpView(
            'my-view-template.php',
            null,
            '/path/to/project/views'
        );
        $this->returnAsIs = true;

        return [
            'postId' => 42,
            'userId' => 24,
        ];
    }
}
```

[link-docs-views]: ./05-views.md
