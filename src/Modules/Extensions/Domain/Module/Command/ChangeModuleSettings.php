<?php

namespace ForkCMS\Modules\Extensions\Domain\Module\Command;

use ForkCMS\Modules\Extensions\Domain\Module\Module;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;

final class ChangeModuleSettings
{
    /** @param array<string, mixed> $defaults */
    public function __construct(public readonly Module $module, private readonly array $defaults)
    {
    }

    public function __get(string $key): mixed
    {
        if ($this->module->getSettings()->has($key)) {
            return $this->module->getSettings()->get($key);
        }

        if (array_key_exists($key, $this->defaults)) {
            return $this->defaults[$key];
        }

        $matches = [];
        if (preg_match($this->getLocaleAgnosticRegexMatch(), $key, $matches)) {
            return $this->defaults[$matches[1]] ?? null;
        }

        return null;
    }

    public function __set(string $key, mixed $value): void
    {
        $this->module->getSettings()->set($key, $value);
    }

    public function __isset(string $key)
    {
        return $this->module->getSettings()->has($key);
    }

    private function getLocaleAgnosticRegexMatch(): string
    {
        static $regex = null;
        if ($regex === null) {
            $localeRegex = implode('|', array_map(static fn (Locale $locale) => $locale->value, Locale::cases()));

            $regex = '/^(.*?)_(' . $localeRegex . ')$/';
        }

        return $regex;
    }
}
