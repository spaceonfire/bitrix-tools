# ORM

В библиотеке `spaceonfire/bitrix-tools` представлены классы для работы с инфоблоками и хайлоад-блоками через ORM D7.

## Инфоблоки

Загрузка данных об элементах и разделах инфоблока вместе с их свойствами и пользовательскими полями поддерживается
только для [инфоблоков 2.0 (с хранением свойств в отдельных таблицах)][link-bitrix-iblocks-2_0].

Для работы с инфоблоком через ORM необходимо создать 4 класса в одном неймспейсе по следующим правилам именования
(чтобы классы знали друг о друге):

-   `<Сущность>Table` - класс для работы с элементами инфоблока
-   `<Сущность>PropSimpleTable` - класс для работы со простыми свойствами элементов инфоблока
-   `<Сущность>PropMultipleTable` - класс для работы со множественными свойствами элементов инфоблока
-   `<Сущность>SectionTable` - класс для работы с разделами инфоблока

Если у нас есть инфоблок с символьным кодом `content`, для него мы можем создать классы следующим образом:

```php
namespace Vendor\Module\Entity;

use spaceonfire\BitrixTools\ORM\IblockElement;
use spaceonfire\BitrixTools\ORM\IblockPropMultiple;
use spaceonfire\BitrixTools\ORM\IblockPropSimple;
use spaceonfire\BitrixTools\ORM\IblockSection;

final class ContentTable extends IblockElement
{
    public static function getIblockCode(): string
    {
        return 'content';
    }
}

final class ContentPropSimpleTable extends IblockPropSimple
{
    public static function getIblockId(): int
    {
        return ContentTable::getIblockId();
    }
}

final class ContentPropMultipleTable extends IblockPropMultiple
{
    public static function getIblockId(): int
    {
        return ContentTable::getIblockId();
    }
}

final class ContentSectionTable extends IblockSection
{
    public static function getIblockId(): int
    {
        return ContentTable::getIblockId();
    }
}
```

### Загружаем элементы инфоблока

При загрузке в `select` можно использовать все поля, указанные в методе `getMap()`.
Классы для работы с элементами инфоблока и их свойствами предоставляют следующие специальные поля:

-   `spaceonfire\BitrixTools\ORM\IblockElement`
    -   `DETAIL_PAGE_URL` - урл страницы элемента, который формируется из настроек инфоблока;
    -   `SECTION` - привязка к разделу, если объявлен класс для работы с разделами;
    -   `SECTIONS` - множественная привязка к разделам;
    -   `PROPERTY_SIMPLE.<символьный код свойства>` - значение простого свойства по его символьному коду;
    -   `PROPERTY_MULTIPLE_<символьный код свойства>.VALUE` - значение множественного свойства (через привязку).

Пример:

```php
use Bitrix\Main\ORM\Query\Filter\ConditionTree;
use Vendor\Module\Entity\ContentTable;

$contentItems = ContentTable::getList([
    'select' => [
        'ID',
        'NAME',
        'DETAIL_PAGE_URL',
        'SOURCE_LINK' => 'PROPERTY_SIMPLE.SOURCE_LINK',
    ],
    'filter' => (new ConditionTree())
        ->where('ACTIVE', 'Y')
])->fetchAll();
```

Важно понимать, что при запросе множественных свойств происходит join отдельной таблицы,
где каждое значение (каждого свойства) является отдельной строкой. Это приводит к дублям в результате запроса.
В этом случае разработчик сам должен позаботиться о дедупликации результата и правильной работе пагинации.

### Загружаем разделы инфоблока

Класс для работы с разделами инфоблока, также предоставляет специальные поля:

-   `DETAIL_PAGE_URL` - урл страницы раздела, который формируется из настроек инфоблока;
-   `PARENT_SECTION` - привязка к родительскому разделу;

Пример:

```php
use Vendor\Module\Entity\ContentSectionTable;

$contentSections = ContentSectionTable::getList([
    'select' => [
        'ID',
        'NAME',
        'DETAIL_PAGE_URL',
        'UF_MY_FIELD',
    ],
])->fetchAll();
```

## Хайлоад блоки

Так как "хайлоад" блоки в Биктриксе являются простыми таблицами, для работы с ними через ORM достаточно одного класса.
Например, для хайлоадблока с названием `MyDictionary` он будет выглядеть так:

```php
namespace Vendor\Module\Entity;

use spaceonfire\BitrixTools\ORM\BaseHighLoadBlockDataManager;

final class MyDictionaryTable extends BaseHighLoadBlockDataManager
{
    /**
     * @inheritDoc
     */
    public static function getHLId()
    {
        return 'MyDictionary';
    }
}
```

### Загружаем элементы хайлоадблока

```php
use Vendor\Module\Entity\MyDictionaryTable;

$items = MyDictionaryTable::getList([
    'select' => [
        'ID',
        'UF_NAME',
        'UF_XML_ID',
    ],
])->fetchAll();
```

### Создаем новый элемент хайлоадблока

```php
use Vendor\Module\Entity\MyDictionaryTable;

$result = MyDictionaryTable::add([
    'UF_NAME' => 'Name',
    'UF_XML_ID' => 'string-id',
    // ...
]);

if (!$result->isSuccess()) {
    throw new RuntimeException(implode('; ', $result->getErrorMessages()));
}
```

### Обновляем элемент хайлоадблока

```php
use Vendor\Module\Entity\MyDictionaryTable;

$result = MyDictionaryTable::update(42, [
    'UF_NAME' => 'Name',
    'UF_XML_ID' => 'string-id',
    // ...
]);

if (!$result->isSuccess()) {
    throw new RuntimeException(implode('; ', $result->getErrorMessages()));
}
```

[link-bitrix-iblocks-2_0]: http://dev.1c-bitrix.ru/learning/course/?COURSE_ID=43&LESSON_ID=2723
