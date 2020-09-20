# Class BaseComponent

Базовый компонент

-   Full name: `\spaceonfire\BitrixTools\Components\BaseComponent`
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

File location: `src/Components/BaseComponent.php:478`

### canShowExceptionMessage()

Определяет можно ли показать сообщение исключения

| Param        | Type         | Description |
| ------------ | ------------ | ----------- |
| `$exception` | _\Throwable_ |             |
| **Return**   | _bool_       |             |

```php
protected function CommonComponentTrait::canShowExceptionMessage(\Throwable $exception): bool
```

File location: `src/Components/BaseComponent.php:408`

### canShowExceptionTrace()

Определяет можно ли показать трейс исключения

| Param        | Type         | Description |
| ------------ | ------------ | ----------- |
| `$exception` | _\Throwable_ |             |
| **Return**   | _bool_       |             |

```php
protected function CommonComponentTrait::canShowExceptionTrace(\Throwable $exception): bool
```

File location: `src/Components/BaseComponent.php:419`

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

File location: `src/Components/BaseComponent.php:380`

### executeComponent()

Выполнение компонента

| Param      | Type                | Description                             |
| ---------- | ------------------- | --------------------------------------- |
| **Return** | _\$this&#124;mixed_ | возвращает текущий экземпляр компонента |

```php
public function BaseComponent::executeComponent(): mixed
```

File location: `src/Components/BaseComponent.php:53`

### executeEpilog()

Выполняется после получения результатов. Не кэшируется

```php
protected function CommonComponentTrait::executeEpilog(): void
```

File location: `src/Components/BaseComponent.php:329`

### executeMain()

Основная логика компонента.

Результат работы метода будет закэширован.

```php
protected function CommonComponentTrait::executeMain(): void
```

File location: `src/Components/BaseComponent.php:315`

### executeProlog()

Выполняется до получения результатов. Не кэшируется

```php
protected function CommonComponentTrait::executeProlog(): void
```

File location: `src/Components/BaseComponent.php:264`

### getId()

Возвращает идентификатор компонента

| Param      | Type     | Description |
| ---------- | -------- | ----------- |
| **Return** | _string_ |             |

```php
public function CommonComponentTrait::getId(): string
```

File location: `src/Components/BaseComponent.php:73`

### getParamsTypes()

Возвращает массив типов для проверки параметров компонента

| Param      | Type                       | Description |
| ---------- | -------------------------- | ----------- |
| **Return** | _\spaceonfire\Type\Type[]_ |             |

```php
protected function CommonComponentTrait::getParamsTypes(): array
```

File location: `src/Components/BaseComponent.php:169`

### getParentParam()

Возвращает значение параметра родительского компонента

| Param        | Type              | Description |
| ------------ | ----------------- | ----------- |
| `$paramName` | _string_          |             |
| **Return**   | _mixed&#124;null_ |             |

```php
final protected function CommonComponentTrait::getParentParam(string $paramName): mixed
```

File location: `src/Components/BaseComponent.php:218`

### includeModules()

Загружает модули 1С-Битрикс.

```php
final protected function CommonComponentTrait::includeModules(): void
```

File location: `src/Components/BaseComponent.php:139`

### init()

Инициализация компонента.

Метод вызывается после вызова конструктора и подключения необходимых модулей.
Служит для выполнения дополнительных настроек.

```php
protected function CommonComponentTrait::init(): void
```

File location: `src/Components/BaseComponent.php:153`

### isAjax()

Проверяет отправлен ли запрос через AJAX

| Param      | Type   | Description |
| ---------- | ------ | ----------- |
| **Return** | _bool_ |             |

```php
public function CommonComponentTrait::isAjax(): bool
```

File location: `src/Components/BaseComponent.php:451`

### onIncludeComponentLang()

Загружает файлы переводов компонента (component.php и class.php)

```php
public function CommonComponentTrait::onIncludeComponentLang(): void
```

File location: `src/Components/BaseComponent.php:84`

### onPrepareComponentParams()

Подготовка параметров компонента

| Param       | Type    | Description |
| ----------- | ------- | ----------- |
| `$arParams` | _array_ |             |
| **Return**  | _array_ |             |

```php
public function CommonComponentTrait::onPrepareComponentParams(mixed $arParams): array
```

File location: `src/Components/BaseComponent.php:95`

### registerCacheTag()

Регистрирует тэг в кэше

| Param  | Type     | Description |
| ------ | -------- | ----------- |
| `$tag` | _string_ |             |

```php
public static function CommonComponentTrait::registerCacheTag(string $tag): void
```

File location: `src/Components/BaseComponent.php:461`

### render()

Рендеринг шаблона компонента

```php
public function CommonComponentTrait::render(): void
```

File location: `src/Components/BaseComponent.php:349`

### renderExceptionMessage()

Отображает сообщение об ошибке

| Param      | Type     | Description |
| ---------- | -------- | ----------- |
| `$message` | _string_ |             |

```php
protected function CommonComponentTrait::renderExceptionMessage(string $message): void
```

File location: `src/Components/BaseComponent.php:433`

### renderExceptionTrace()

Отображает трейс ошибки

| Param        | Type         | Description |
| ------------ | ------------ | ----------- |
| `$throwable` | _\Throwable_ |             |

```php
protected function CommonComponentTrait::renderExceptionTrace(\Throwable $throwable): void
```

File location: `src/Components/BaseComponent.php:442`

### return404()

Выбрасывает NotFoundException

| Param        | Type                   | Description                                                                 |
| ------------ | ---------------------- | --------------------------------------------------------------------------- |
| `$throwable` | _\Throwable&#124;null_ | Исходное исключение. При наличии будет использовано его сообщение об ошибке |

```php
final protected function CommonComponentTrait::return404(?\Throwable $throwable = null): void
```

File location: `src/Components/BaseComponent.php:359`

### run()

Универсальный порядок выполнения простого компонента

```php
final protected function BaseComponent::run(): void
```

File location: `src/Components/BaseComponent.php:24`

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

File location: `src/Components/BaseComponent.php:490`

---

This file automatically generated by [Simple PHP ApiDoc](https://github.com/spaceonfire/simple-php-apidoc)
