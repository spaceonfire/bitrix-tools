# Компоненты

Библиотека `spaceonfire/bitrix-tools` предоставляет базовые классы, нацеленные на упрощение разработки собственных
компонентов Битрикс. Подробнее о технологии можно почитать в [курсе Битрикса][link-bitrix-components].

Эти классы реализуют проверка параметров и универсальный порядок выполнения с обработкой [исключений][php-exceptions].

## Простой базовый компонент

Обычный компонент, предназначенный для работы с данными, представлен классом `spaceonfire\BitrixTools\Components\BaseComponent`.

### Проверка параметров

В первую очередь базовый компонент позволяет производить проверку переданных параметров на соответствие типа.
Чтобы задать правила проверки параметров компонента, необходимо перегрузить метод `getParamsTypes()`.
Правила описываются в виде массива, ключами которого являются названия параметров, а значениями - объекты,
реализующие интерфейс `spaceonfire\Type\Type` из библиотеки [`spaceonfire/type`][spaceonfire-type].

Библиотека `spaceonfire/type` предоставляет широкие возможности для описания ожидаемого типа параметра, например:

-   Встроенные типы PHP (int, float и string могут быть указаны как не строгие с возможностью привидения к указанному типу)
-   Проверка экземпляра класса (instanceof)
-   Комбинирование типов (конъюнкция и дизъюнкция)
-   Коллекционный тип, который проверяет итерируемые параметры на соответствие типов ключей и значений

```php
use spaceonfire\BitrixTools\Components\BaseComponent;
use spaceonfire\Type\BuiltinType;
use spaceonfire\Type\DisjunctionType;

class MyComponent extends BaseComponent
{
    protected function getParamsTypes(): array
    {
        return [
            'REQUIRED_INT_PARAM' => new BuiltinType(BuiltinType::INT, false),
            'REQUIRED_STRING_PARAM' => new BuiltinType(BuiltinType::STRING, false),
            'NULLABLE_STRING_PARAM' => new DisjunctionType([
                new BuiltinType(BuiltinType::STRING, false),
                new BuiltinType(BuiltinType::NULL),
            ]),
        ];
    }
}
```

Пример выше определяет следующие проверки типов для параметров:

-   `REQUIRED_INT_PARAM` - обязательный целочисленный параметр с возможностью приведения значения.
    Т.е. если в параметр значение было передано в виде строки, например `'42'`, оно будет приведено к целочисленному типу.
-   `REQUIRED_STRING_PARAM` - обязательный строковый параметр с возможностью приведения значения.
-   `NULLABLE_STRING_PARAM` - не обязательный строковый параметр с возможностью приведения значения. Проверяется, что
    параметр является строкой или `null`.

### Порядок выполнения

Метод выполнения компонента `executeComponent()` содержит следующий код:

```php
try {
    $this->run();
} catch (Throwable $e) {
    $this->catchError($e);
}
return $this;
```

Как можно заметить выполнение компонента, происходит в методе `run()`, его мы рассмотрим чуть позже. Любое исключение,
выброшенное в процессе выполнения будет обработано в `catchError()`. И наконец возвращается текущий экземпляр компонента,
это позволяет использовать информацию из него после выполнения, например в другом компоненте:

```php
/** @global $APPLICATION CMain */
$myFooComponent = $APPLICATION->IncludeComponent('my:foo.component', '', []);
$APPLICATION->IncludeComponent('my:bar.component', '', [
    'FOO_DATA' => $myFooComponent->getSomeData(),
]);
```

Рассмотрим, что происходит в методе `run()`, в порядке его выполнения:

1. `includeModules()` - подключает модули, указанные в свойстве `needModules` класса компонента.
1. `init()` - инициализация компонента
1. `executeProlog()` - пролог компонента
1. `startCache()` - запуск кэширования
    1. `executeMain()` - основная логика компонента
    1. `render()` - подключение шаблона компонента. По-умолчанию рендер шаблона кэшируется.
       Чтобы кэшировалась только логика без шаблона, необходимо установить свойство компонента `cacheTemplate` = `false`
    1. `writeCache()` - запись результатов в кэш
1. `render()` - подключение шаблона компонента, если кэширование шаблона отключено свойством `cacheTemplate`
1. `executeEpilog()` - эпилог компонента

> Хотя перегрузка метода `executeComponent()` не рекомендуется, в редких случаях предлагаемый порядок выполнения может
> не подходить для компонента. Тогда это допустимо под ответственность разработчика.

### Обработка исключений

Базовые компоненты используют исключения для контроля хода выполнения компонента и обработки непредвиденных ситуаций.

Если компонент не может продолжать свою работу, нужно выбросить исключение, которое будет перехвачено и обработано:

```php
class MyComponent extends spaceonfire\BitrixTools\Components\BaseComponent
{
    protected function executeProlog(): void
    {
        throw new RuntimeException('Some error occurred');
    }
}
```

Обработка исключения происходит в методе `catchError()` следующим образом:

1. Во-первых, сбрасывается кэш
1. Выставляется HTTP статус исходя из исключения,
   можно выбросить исключение из библиотеки [`narrowspark/http-status`][narrowspark-http-status]
1. Вывод сообщения об ошибке
1. Запись исключения в `ExceptionHandler` Битрикса

Заострим внимание на вывод сообщения об ошибке:

-   Вывод основного сообщения об ошибке производится методом `renderExceptionMessage()`
-   По-умолчанию для обычных пользователей выводится общее сообщение об ошибке, а для админов - сообщение из исключения.
    В каких ситуациях выводить сообщение из исключения можно определить в методе `canShowExceptionMessage()`
-   Далее при включенном режиме отладки для админов выводится трейс исключения. Переопределить, в каких ситуациях следует
    показывать трейс, можно в методе `canShowExceptionTrace()`. А за вывод отвечает метод `renderExceptionTrace()`

### Кэширование

Кеширование базового компонента включается автоматически, если ему передать соответствующие параметры `CACHE_TYPE` и `CACHE_TIME`.
Кешируется только метод `executeMain()` и шаблон. Если кеширование шаблона необходимо отключить, нужно установить
свойство класса компонента `cacheTemplate` в значение `false`.

Базовый компонент может разделять кеш для разных групп пользователей.
Для этого установите параметр компонента `CACHE_GROUPS = Y`.

Так же можно добавить дополнительный идентификатор кеша с помощью метода `addCacheAdditionalId($id)`.
Для тегирования кэша компонента пользуйтесь методом `registerCacheTag($tag)`. Пометив таким образом кеш,
в будущем его можно сбросить его по тегу:

```php
Bitrix\Main\Application::getInstance()->getTaggedCache()->clearByTag('my-cache-tag');
```

## Комплексный базовый компонент (роутер)

Комплексный компонент, предназначенный для динамического роутинга между страницами,
представлен классом `spaceonfire\BitrixTools\Components\BaseRouterComponent`.

### Проверка параметров

Проверка параметров реализована так же, как и в простом базовом компоненте. См. выше.

### Порядок выполнения

Метод выполнения компонента `executeComponent()` аналогичен простому базовому компоненту,
поэтому сразу перейдем к разбору метода `run()`:

1. `includeModules()` - подключает модули, указанные в свойстве `needModules` класса компонента.
1. `init()` - инициализация компонента
1. `setSefDefaultParams()` - установка параметров ЧПУ по-умолчанию
1. `setPage()` - определение запрошенной страницы
1. `executeProlog()` - пролог компонента
1. `executeMain()` - основная логика компонента: в `$arResult` добавляются полученные переменны запроса.
1. `render()` - подключение шаблона компонента
1. `executeEpilog()` - эпилог компонента

Так как комплексные компоненты обычно реализуют только роутинг, а не работают с данными,
то кэширование здесь не включается.

### Обработка исключений

Обработка исключений реализована так же, как и в простом базовом компоненте. См. выше.

## Виртуальные свойства для компонентов

Для базовых компонентов библиотеки можно включить использование виртуальных свойств.
К таким свойствам можно обращаться как к обычным свойствам класса, только они хранятся в `$arResult` компонента.
Такой подход позволяет отслеживать использование свойств в коде проекта, при этом сохраняя значения в кэше.

Для работы с виртуальными свойствами необходимо подключить к проекту библиотеку [`phpdocumentor/reflection-docblock`][phpdocumentor-reflection-docblock]:

```bash
composer require phpdocumentor/reflection-docblock
```

После этого используем трейт `spaceonfire\BitrixTools\Components\Property\ComponentPropertiesTrait`,
указываем виртуальные свойства в DocBlock и используем их:

```php
// В компоненте: class.php

use spaceonfire\BitrixTools\Components\BaseComponent;
use spaceonfire\BitrixTools\Components\Property\ComponentPropertiesTrait;

/**
 * My Component
 *
 * @property string $property
 * @property-write int $writeProperty
 * @property-read array $readProperty
 */
class MyComponent extends BaseComponent
{
    use ComponentPropertiesTrait;

    protected function executeMain(): void
    {
        parent::executeMain();

        $this->property = 'Hello from component';
        $this->writeProperty = 1;
        $this->readProperty = [['id' => 42, 'name' => 'Main']];
    }
}

// В шаблоне: template.php
/**
 * @var MyComponent $component
 */

echo $component->property; // Hello from component
$component->property = 'Hello from template';

$component->writeProperty = 2;

var_dump($this->readProperty);
```

К виртуальным свойствам можно ограничить доступ: только на чтение (`@property-read`) или только на запись (`@property-write`).
Это ограничение не действует внутри класса компонента.

[link-bitrix-components]: https://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=43&CHAPTER_ID=04565&LESSON_PATH=3913.4565
[php-exceptions]: https://php.net/manual/ru/language.exceptions.php
[spaceonfire-type]: https://github.com/spaceonfire/type
[narrowspark-http-status]: https://github.com/narrowspark/http-status
[phpdocumentor-reflection-docblock]: https://github.com/phpDocumentor/ReflectionDocBlock
