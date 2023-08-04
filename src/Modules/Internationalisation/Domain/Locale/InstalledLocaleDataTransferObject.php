<?php

namespace ForkCMS\Modules\Internationalisation\Domain\Locale;

use ArrayAccess;

/**
 * @implements ArrayAccess<string, null|Locale|bool|array<string,mixed>>
 */
final class InstalledLocaleDataTransferObject implements ArrayAccess
{
    public ?Locale $locale = null;
    public bool $isEnabledForWebsite = true;
    public bool $isDefaultForWebsite = false;
    public bool $isEnabledForBrowserLocaleRedirect = true;
    public bool $isEnabledForUser = true;
    public bool $isDefaultForUser = false;
    /** @var array<string,mixed> */
    public array $settings;

    public function __construct(protected ?InstalledLocale $installedLocale = null)
    {
        if ($installedLocale === null) {
            $this->settings = [
                'date_format_short' => $_ENV['FORK_DEFAULT_DATE_FORMAT_SHORT'],
                'date_format_long' => $_ENV['FORK_DEFAULT_DATE_FORMAT_LONG'],
                'time_format' => $_ENV['FORK_DEFAULT_TIME_FORMAT'],
                'number_format' => $_ENV['FORK_DEFAULT_NUMBER_FORMAT'],
                'date_time_order' => $_ENV['FORK_DEFAULT_DATE_TIME_ORDER'],
            ];
            return;
        }

        $this->locale = $installedLocale->getLocale();
        $this->isEnabledForWebsite = $installedLocale->isEnabledForWebsite();
        $this->isDefaultForWebsite = $installedLocale->isDefaultForWebsite();
        $this->isEnabledForBrowserLocaleRedirect = $installedLocale->isEnabledForBrowserLocaleRedirect();
        $this->isEnabledForUser = $installedLocale->isEnabledForUser();
        $this->isDefaultForUser = $installedLocale->isDefaultForUser();
        $this->settings = $installedLocale->getSettings()->all();
    }

    public static function fromLocale(Locale $locale): self
    {
        $installedLocale = new self();
        $installedLocale->locale = $locale;

        return $installedLocale;
    }

    public function hasEntity(): bool
    {
        return $this->installedLocale !== null;
    }

    public function getEntity(): InstalledLocale
    {
        return $this->installedLocale;
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->$offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->$offset;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->$offset = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->$offset);
    }
}
