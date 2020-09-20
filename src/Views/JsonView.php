<?php

namespace spaceonfire\BitrixTools\Views;

use Bitrix\Main\Text\Encoding;

/**
 * JSON MVC view
 */
class JsonView extends BaseView
{
    /**
     * @var int options for json_encode
     */
    private $options = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE;

    /**
     * Создает новый MVC JSON view
     * @param mixed $data Данные view
     * @param int|null $options
     */
    public function __construct($data = [], ?int $options = null) {
        parent::__construct('', $data);

        if ($options !== null) {
            $this->options = $options;
        }
    }

    /** {@inheritDoc} */
    public function sendHeaders(): void
    {
        header('Content-type: application/json');
    }

    /** {@inheritDoc} */
    public function render(): string
    {
        $result = json_encode($this->data, $this->options);

        if (defined('SITE_CHARSET') && strtoupper(SITE_CHARSET) !== 'UTF-8') {
            return Encoding::convertEncoding($result, 'UTF-8', SITE_CHARSET);
        }

        return $result;
    }
}
