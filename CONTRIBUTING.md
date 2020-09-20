# Помощь в разработке

Если Вы заинтересованы в развитии проекта, Вы можете помочь нам в этом.
Не стесняйтесь [оставлять вопросы (issue)][link-issues], если столкнулись с трудностями при использовании библиотеки,
обнаружили баг или хотите предложить новые функции.
Так же Вы можете помочь, решая существующие issue и присылая pull request.

## Необходимые условия окружения

Для работы над проектом в системе должны быть установлены:

- PHP 7.2 или выше
- Composer
- Редактор кода с поддержкой EditorConfig
- Node.js LTS с NPM и `npx` (опционально, для форматирования документации)

## Стиль кода

Стиль кода на проекте следует стандарту [PSR-12][link-psr-12].

## Ветвление в Git

Для управления ветками используется подход [GitHub Flow][link-github-flow].
Ветки следует называть по шаблону `{issue-id}-{short-branch-name}`, например `12-refactor-authentication`.
Ветки должны создаваться от актуального состояния `master`. Работу над pull request'ом следует вести в собственном форке.

## Скрипты

### Сборка документации

```bash
composer run doc
```

### Проверка стиля кода

```bash
composer run codestyle
```

[link-issues]: https://github.com/spaceonfire/bitrix-tools/issues
[link-psr-12]: https://www.php-fig.org/psr/psr-12/
[link-github-flow]: https://guides.github.com/introduction/flow/
