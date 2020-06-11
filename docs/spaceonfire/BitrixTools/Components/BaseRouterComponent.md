# Class BaseRouterComponent

Комплексный компонент (роутер)

-   Full name: `\spaceonfire\BitrixTools\Components\BaseRouterComponent`
-   This class uses: `\spaceonfire\BitrixTools\Components\CommonComponentTrait`

## Methods

### addCacheAdditionalId()

Добавляет дополнительный ID для кэша

| Param | Type    | Description |
| ----- | ------- | ----------- |
| `$id` | _mixed_ |             |

```php
final protected function CommonComponentTrait::addCacheAdditionalId(mixed $id): void
```

File location: `src/Components/BaseRouterComponent.php:459`

### buildUrl()

Собирает Url из шаблона

| Param           | Type               | Description |
| --------------- | ------------------ | ----------- |
| `$templateName` | _string_           |             |
| `$params`       | _array_            |             |
| **Return**      | _string&#124;null_ |             |

```php
public function BaseRouterComponent::buildUrl(string $templateName, array $params = []): ?string
```

File location: `src/Components/BaseRouterComponent.php:217`

### canShowExceptionMessage()

Определяет можно ли показать сообщение исключения

| Param        | Type         | Description |
| ------------ | ------------ | ----------- |
| `$exception` | _\Throwable_ |             |
| **Return**   | _bool_       |             |

```php
protected function CommonComponentTrait::canShowExceptionMessage(\Throwable $exception): bool
```

File location: `src/Components/BaseRouterComponent.php:389`

### canShowExceptionTrace()

Определяет можно ли показать трейс исключения

| Param        | Type         | Description |
| ------------ | ------------ | ----------- |
| `$exception` | _\Throwable_ |             |
| **Return**   | _bool_       |             |

```php
protected function CommonComponentTrait::canShowExceptionTrace(\Throwable $exception): bool
```

File location: `src/Components/BaseRouterComponent.php:400`

### catchError()

Вызывается при возникновении ошибки

Сбрасывает кэш, показывает сообщение об ошибке (в общем виде для пользователей и детально для админов),
пишет ошибку в лог Битрикса

| Param        | Type         | Description |
| ------------ | ------------ | ----------- |
| `$exception` | _\Throwable_ |             |

```php
protected function CommonComponentTrait::catchError(\Throwable $exception): void
```

File location: `src/Components/BaseRouterComponent.php:361`

### executeComponent()

Выполнение компонента

| Param      | Type                | Description                              |
| ---------- | ------------------- | ---------------------------------------- |
| **Return** | _\$this&#124;mixed_ | возвращает экземпляр текущего компонента |

```php
public function BaseRouterComponent::executeComponent(): mixed
```

File location: `src/Components/BaseRouterComponent.php:176`

### executeEpilog()

Выполняется после получения результатов. Не кэшируется

```php
protected function CommonComponentTrait::executeEpilog(): void
```

File location: `src/Components/BaseRouterComponent.php:318`

### executeMain()

Основная логика компонента.

Результат работы метода будет закэширован.

```php
protected function CommonComponentTrait::executeMain(): void
```

File location: `src/Components/BaseRouterComponent.php:306`

### executeProlog()

Выполняется до получения результатов. Не кэшируется

```php
protected function CommonComponentTrait::executeProlog(): void
```

File location: `src/Components/BaseRouterComponent.php:255`

### getId()

Возвращает идентификатор компонента

| Param      | Type     | Description |
| ---------- | -------- | ----------- |
| **Return** | _string_ |             |

```php
public function CommonComponentTrait::getId(): string
```

File location: `src/Components/BaseRouterComponent.php:69`

### getParamsTypes()

Возвращает массив типов для проверки параметров компонента

| Param      | Type                       | Description |
| ---------- | -------------------------- | ----------- |
| **Return** | _\spaceonfire\Type\Type[]_ |             |

```php
protected function CommonComponentTrait::getParamsTypes(): array
```

File location: `src/Components/BaseRouterComponent.php:158`

### getParentParam()

Возвращает значение параметра родительского компонента

| Param        | Type              | Description |
| ------------ | ----------------- | ----------- |
| `$paramName` | _string_          |             |
| **Return**   | _mixed&#124;null_ |             |

```php
final protected function CommonComponentTrait::getParentParam(string $paramName): mixed
```

File location: `src/Components/BaseRouterComponent.php:209`

### getUrlTemplate()

Возвращает шаблон Url по названию

| Param           | Type               | Description |
| --------------- | ------------------ | ----------- |
| `$templateName` | _string_           |             |
| **Return**      | _string&#124;null_ |             |

```php
public function BaseRouterComponent::getUrlTemplate(string $templateName): ?string
```

File location: `src/Components/BaseRouterComponent.php:201`

### hasUrlTemplate()

Проверяет, объявлен ли шаблон Url компонентом в `defaultUrlTemplates404`

| Param           | Type     | Description |
| --------------- | -------- | ----------- |
| `$templateName` | _string_ |             |
| **Return**      | _bool_   |             |

```php
public function BaseRouterComponent::hasUrlTemplate(string $templateName): bool
```

File location: `src/Components/BaseRouterComponent.php:191`

### init()

Инициализация компонента.

Метод вызывается после вызова конструктора и подключения необходимых модулей.
Служит для выполнения дополнительных настроек.

```php
protected function CommonComponentTrait::init(): void
```

File location: `src/Components/BaseRouterComponent.php:146`

### isAjax()

Проверяет отправлен ли запрос через AJAX

| Param      | Type   | Description |
| ---------- | ------ | ----------- |
| **Return** | _bool_ |             |

```php
public function CommonComponentTrait::isAjax(): bool
```

File location: `src/Components/BaseRouterComponent.php:432`

### isSearchRequest()

Проверяет был ли выполнен поисковой запрос

| Param      | Type   | Description |
| ---------- | ------ | ----------- |
| **Return** | _bool_ |             |

```php
protected function BaseRouterComponent::isSearchRequest(): bool
```

File location: `src/Components/BaseRouterComponent.php:67`

### onIncludeComponentLang()

Загружает файлы переводов компонента (component.php и class.php)

```php
public function CommonComponentTrait::onIncludeComponentLang(): void
```

File location: `src/Components/BaseRouterComponent.php:80`

### onPrepareComponentParams()

Подготовка параметров компонента

| Param       | Type    | Description |
| ----------- | ------- | ----------- |
| `$arParams` | _array_ |             |
| **Return**  | _array_ |             |

```php
public function CommonComponentTrait::onPrepareComponentParams(array $arParams): array
```

File location: `src/Components/BaseRouterComponent.php:91`

### registerCacheTag()

Регистрирует тэг в кэше

| Param  | Type     | Description |
| ------ | -------- | ----------- |
| `$tag` | _string_ |             |

```php
public static function CommonComponentTrait::registerCacheTag(string $tag): void
```

File location: `src/Components/BaseRouterComponent.php:442`

### render()

Рендеринг шаблона компонента

```php
public function CommonComponentTrait::render(): void
```

File location: `src/Components/BaseRouterComponent.php:338`

### renderExceptionMessage()

Отображает сообщение об ошибке

| Param      | Type     | Description |
| ---------- | -------- | ----------- |
| `$message` | _string_ |             |

```php
protected function CommonComponentTrait::renderExceptionMessage(string $message): void
```

File location: `src/Components/BaseRouterComponent.php:414`

### renderExceptionTrace()

Отображает трейс ошибки

| Param        | Type         | Description |
| ------------ | ------------ | ----------- |
| `$throwable` | _\Throwable_ |             |

```php
protected function CommonComponentTrait::renderExceptionTrace(\Throwable $throwable): void
```

File location: `src/Components/BaseRouterComponent.php:423`

### return404()

Выбрасывает NotFoundException

| Param        | Type                   | Description                                                                 |
| ------------ | ---------------------- | --------------------------------------------------------------------------- |
| `$throwable` | _\Throwable&#124;null_ | Исходное исключение. При наличии будет использовано его сообщение об ошибке |

```php
final protected function CommonComponentTrait::return404(?\Throwable $throwable = null): void
```

File location: `src/Components/BaseRouterComponent.php:348`

### run()

Универсальный порядок выполнения комплексного компонента

```php
final public function BaseRouterComponent::run(): void
```

File location: `src/Components/BaseRouterComponent.php:155`

### setPage()

Устанавливает тип запрошенной страницы и создает переменные из шаблонов URL

```php
protected function BaseRouterComponent::setPage(): void
```

File location: `src/Components/BaseRouterComponent.php:77`

### setSefDefaultParams()

Устанавливает параметры по-умолчанию для ЧПУ

```php
protected function BaseRouterComponent::setSefDefaultParams(): void
```

File location: `src/Components/BaseRouterComponent.php:53`

### triggerEvent()

Вызывает событие, специфичное для компонента

| Param      | Type                             | Description                                                          |
| ---------- | -------------------------------- | -------------------------------------------------------------------- |
| `$type`    | _string_                         | Тип события. Имя класса компонента будет добавлено в виде префикса.  |
| `$params`  | _array_                          | Параметры события. Параметр `component` будет добавлен автоматически |
| `$filter`  | _null&#124;string&#124;string[]_ | Фильтр события                                                       |
| **Return** | _\Bitrix\Main\Event_             |                                                                      |

```php
final protected function CommonComponentTrait::triggerEvent(string $type, array $params = [], mixed $filter = null): \Bitrix\Main\Event
```

File location: `src/Components/BaseRouterComponent.php:471`

---

This file automatically generated by [Simple PHP ApiDoc](https://github.com/spaceonfire/simple-php-apidoc)
