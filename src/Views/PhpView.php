<?php

namespace spaceonfire\BitrixTools\Views;

use Bitrix\Main\IO\FileNotFoundException;
use Bitrix\Main\Localization\Loc;

/**
 * PHP MVC view
 */
class PhpView extends BaseView
{
    /** {@inheritDoc} */
    public function sendHeaders(): void
    {
        header('Content-type: text/html; charset=' . SITE_CHARSET);
    }

    /**
     * {@inheritDoc}
     * @throws FileNotFoundException
     */
    public function render(): string
    {
        $path = $this->getPath();
        if (!is_file($path)) {
            throw new FileNotFoundException($path);
        }

        Loc::loadMessages($path);

        ob_start();
        $result = &$this->data;
        require $path;
        return ob_get_clean();
    }

    /**
     * Выводит HTML в безопасном виде
     * @param string $data Выводимые данные
     * @return string
     */
    public function escape($data): string
    {
        return htmlspecialchars($data);
    }
}
