<?php

namespace Common\Core\Header;

use InvalidArgumentException;

final class JsData
{
    /** @var array */
    private $jsData;

    /**
     * @param array $initialData
     */
    public function __construct(array $initialData = [])
    {
        $this->jsData = $initialData;
    }

    /**
     * @param string $module The name of the module.
     * @param string $key The key where under the value will be stored.
     * @param mixed $value The value
     *
     * @throws InvalidArgumentException when trying to overwrite the language
     */
    public function add(string $module, string $key, $value)
    {
        if ($module === 'language') {
            throw new InvalidArgumentException('You are not allowed to overwrite the language');
        }

        $this->jsData[$module][$key] = $value;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return '<script>var jsData = ' . json_encode($this->jsData) . '</script>';
    }
}
