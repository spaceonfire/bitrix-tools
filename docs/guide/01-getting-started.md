# Начало работы

## Установка

Установка пакета с использованием Composer:

```bash
composer require spaceonfire/bitrix-tools
```

## Конфигурация

Рекомендуется производить подключение и настройку пакета в файле [`local/php_inteface/init.php`][link-bitrix-init-php].

### Автолоадинг

Чтобы подключить пакеты Composer в проект, необходимо подключить автолоадер от Composer в `init.php`.

```php
require_once '/path/to/vendor/autoload.php';
```

При запуске в регистронезависимой файловой системе, например при разработке на Windows, может возникнуть конфликт между
[лоадером Битрикса][link-bxapi-loader] и лоадером Composer. Из-за того что битриксовый лоадер всегда конвертирует имя
файла в нижний регистр (нарушая [PSR-4][link-psr-4]), иногда возникает баг повторного подключения файла разными лоадерами.
Чтобы решить эту проблему, можно подключить пропатченный лоадер Composer вместо стандартного,
который в первую очередь пытается подключить файл в нижнем регистре:

```php
require_once '/path/to/vendor/spaceonfire/bitrix-tools/resources/autoload.php';
```

### Запуск на нестандартном порту

Когда веб-сервер проекта запускается за прокси-сервером на нестандартном порту (отличный от 80 или 443),
Битрикс при [редиректе][link-bxapi-localredirect] подставляет этот порт в урл. Для решения этой проблемы в `init.php`
можно добавить следующий код:

```php
spaceonfire\BitrixTools\Common::trustProxy();
```

### Отключение http авторизации на сайте

Если тестовый сайт скрыт за http авторизацией, можно отключить попытку авторизоваться на сайте с этими данными,
добавив следующий код в `init.php`:

```php
spaceonfire\BitrixTools\Common::disableHttpAuth();
```

### Корректная установка HTTP статуса Битриксом

Если сайт запускается на связке nginx + php-fpm, вместо привычной Битриксу, но устаревшей, связки nginx + apache,
функция [`CHTTP::SetStatus()`][link-bxapi-chttp-setstatus] будет устанавливать некорректный заголовок статуса.
Чтобы это исправить, необходимо установить константу `BX_HTTP_STATUS`:

```php
define('BX_HTTP_STATUS', true);
```

### Пример

Полный пример файла `init.php`, можно найти в файле репозитория `/examples/init.php`.

[link-bitrix-init-php]: https://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=43&LESSON_ID=2916&LESSON_PATH=3913.4776.2916
[link-psr-4]: https://www.php-fig.org/psr/psr-4/
[link-bxapi-localredirect]: https://bxapi.ru/src/?module_id=main&name=LocalRedirect
[link-bxapi-loader]: https://bxapi.ru/src/?module_id=main&name=Loader::autoLoad
[link-bxapi-chttp-setstatus]: https://bxapi.ru/src/?module_id=main&name=CHTTP::SetStatus
