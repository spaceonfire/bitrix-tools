<?php

namespace spaceonfire\BitrixTools\Views;

/**
 * HTML MVC view
 */
class HtmlView extends BaseView
{
    /**
     * Создает новый MVC HTML view
     * @param string $data HTML текст
     */
    public function __construct($data = '')
    {
        parent::__construct('', $data);
    }

    /** {@inheritDoc} */
    public function sendHeaders(): void
    {
        header('Content-type: text/html; charset=' . SITE_CHARSET);
    }

    /** {@inheritDoc} */
    public function render(): string
    {
        return is_array($this->data) ? implode('', $this->data) : (string)$this->data;
    }
}
