<?php

namespace ForkCMS\Core\Domain\Settings;

use DateTimeImmutable;
use DateTimeZone;
use JsonSerializable;

use function array_key_exists;
use function strlen;

final class SettingsBag implements JsonSerializable
{
    /** @var array<string, mixed> */
    private array $settings = [];

    private bool $hasChanges = false;

    /**
     * @param array<string, mixed> $settings
     */
    public function __construct(array $settings = [])
    {
        $this->add($settings);
    }

    public function clear(): void
    {
        if (count($this->settings) > 0) {
            $this->hasChanges = true;
        }

        $this->settings = [];
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function add(array $parameters): void
    {
        foreach ($parameters as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->settings;
    }

    public function get(string $name): mixed
    {
        if (array_key_exists($name, $this->settings)) {
            return $this->settings[$name];
        }

        if (!$name) {
            throw new SettingNotFoundException($name);
        }

        $alternatives = [];
        foreach ($this->settings as $key => $parameterValue) {
            $lev = levenshtein($name, $key);
            if ($lev <= strlen($name) / 3 || str_contains($key, $name)) {
                $alternatives[] = $key;
            }
        }

        throw new SettingNotFoundException($name, null, $alternatives);
    }

    public function getOr(string $name, mixed $default = null): mixed
    {
        try {
            return $this->get($name);
        } catch (SettingNotFoundException) {
            return $default;
        }
    }

    public function set(string $name, mixed $value): void
    {
        // check if the value is a json encoded datetime
        if (
            is_array($value)
            && count($value) === 3
            && isset($value['date'], $value['timezone'], $value['timezone_type'])
            && $value['timezone_type'] === 3
        ) {
            $value = new DateTimeImmutable($value['date'], new DateTimeZone($value['timezone']));
        }

        if (!array_key_exists($name, $this->settings) || $this->settings[$name] !== $value) {
            $this->hasChanges = true;
        }

        $this->settings[$name] = $value;
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->settings);
    }

    public function remove(string $name): void
    {
        $this->hasChanges = true;
        unset($this->settings[$name]);
    }

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        return $this->all();
    }

    public function hasChanges(): bool
    {
        return $this->hasChanges;
    }

    public function asJsonString(): string
    {
        return json_encode($this, JSON_THROW_ON_ERROR);
    }

    public static function fromJsonString(string $value): self
    {
        return new SettingsBag(json_decode($value, true, 512, JSON_THROW_ON_ERROR));
    }
}
