# Changelog

Все значимые изменения в `bitrix-tools` должны быть задокументированы в данном файле.

Обновления должны следовать принципам [Keep a CHANGELOG](http://keepachangelog.com/).

<!--
## [X.Y.Z] - YYYY-MM-DD
### Добавлено
- Nothing

### Устаревшее
- Nothing

### Исправлено
- Nothing

### Удалено
- Nothing

### Безопасность
- Nothing
-->

## [0.8.0] - 2020-06-11
### Добавлено
- Переделаны CacheMap'ы, удалена зависимость от `jeremeamia/superclosure`
- Добавлена абстракция для агентов
- Отрефакторены классы для работы с ORM
- Класс `ORMTools`
- Исключения 1С-Битрикс заменены на исключения SPL
- Добавлена возможность задавать свойства для компонента (`ComponentPropertiesTrait`)
- Обновлена проверка параметров компонента
- Добавлен пропатченный автолоадер
- Добавлены методы `Common::getAppException()`, `Common::trustProxy()` и `Common::disableHttpAuth()`

### Устаревшее
- Возвращены и помечены как устаревшие классы `spaceonfire\BitrixTools\Mvc`
