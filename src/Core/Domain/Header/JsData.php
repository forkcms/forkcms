<?php

namespace ForkCMS\Core\Domain\Header;

use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;

final class JsData
{
    /** @param mixed[] $jsData */
    public function __construct(private array $jsData = [])
    {
    }

    public function add(ModuleName $module, string $key, mixed $value): void
    {
        $this->jsData[$module->getName()][$key] = $value;
    }

    public function __toString(): string
    {
        return '<script>var jsData = ' . json_encode($this->jsData, JSON_THROW_ON_ERROR) . '</script>';
    }
}
