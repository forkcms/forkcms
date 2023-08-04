<?php

namespace ForkCMS\Modules\Extensions\Domain\Module;

use ForkCMS\Modules\Extensions\Domain\Module\Event\ModuleInstalledEvent;
use InvalidArgumentException;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ModuleSettings implements EventSubscriberInterface
{
    public function __construct(
        private readonly ModuleRepository $moduleRepository,
        private readonly CacheItemPoolInterface $cache
    ) {
    }

    /**
     * Get a module setting
     *
     * @param ModuleName $module The module wherefore a setting has to be retrieved.
     * @param string $key The name of the setting to be retrieved.
     * @param mixed $defaultValue A fallback value
     */
    public function get(ModuleName $module, string $key, mixed $defaultValue = null): mixed
    {
        $settings = $this->getSettings();

        return $settings[$module->getName()][$key] ?? $defaultValue;
    }

    /**
     * Store a module setting
     *
     * @param ModuleName $moduleName The module wherefore a setting has to be stored.
     * @param string $key The name of the setting.
     * @param mixed $value The value to save
     */
    public function set(ModuleName $moduleName, string $key, mixed $value): void
    {
        $module = $this->getModule($moduleName);
        $module->setSetting($key, $value);
        $this->moduleRepository->save($module);

        $this->invalidateCache();
    }

    /**
     * Deletes a module setting
     *
     * @param ModuleName $moduleName
     * @param string $key
     */
    public function delete(ModuleName $moduleName, string $key): void
    {
        $module = $this->getModule($moduleName);
        $module->removeSetting($key);
        $this->moduleRepository->save($module);

        $this->invalidateCache();
    }

    /**
     * Get all module settings for a module
     *
     * @param ModuleName $moduleName The module wherefore a setting has to be retrieved.
     *
     * @return array<string, mixed>
     */
    public function getForModule(ModuleName $moduleName): array
    {
        $settings = $this->getSettings();
        $name = $moduleName->getName();

        if (isset($settings[$name])) {
            return $settings[$name];
        }

        $this->invalidateCache();

        $settings = $this->getSettings();

        return $settings[$name] ?? [];
    }

    /**
     * Fetches all the settings
     *
     * @return array<string,array<string,mixed>>
     */
    private function getSettings(): array
    {
        $item = $this->cache->getItem(self::getCacheKey());
        if ($item->isHit()) {
            return $item->get();
        }

        $settings = $this->getAllSettingsFromDatabase();
        $item->set($settings);
        $this->cache->save($item);

        return $settings;
    }

    /**
     * Reads all the settings from the database and groups them by module
     *
     * @return array<string, array<string, mixed>>
     */
    private function getAllSettingsFromDatabase(): array
    {
        $modules = $this->moduleRepository->findAll();

        // loop settings & unserialize the values
        $groupedSettings = [];
        foreach ($modules as $module) {
            $groupedSettings[$module->getName()->getName()] = $module->getSettings()->all();
        }

        return $groupedSettings;
    }


    public function getModule(ModuleName $moduleName): Module
    {
        static $modules = [];
        $name = $moduleName->getName();

        $modules[$name] = $modules[$name]
            ?? $this->moduleRepository->find($moduleName)
            ?? throw new InvalidArgumentException('Module name not found: ' . $name);

        return $modules[$name];
    }

    /**
     * Instead of invalidating the cache, we could also fetch existing
     * settings, update them & re-store them to cache. That would save
     * us the next query to repopulate the cache.
     * However, there could be race conditions where 2 concurrent
     * requests write at the same time and one ends up overwriting the
     * other (unless we do a CAS, but PSR-6 doesn't support that)
     * Clearing cache will be safe: in the case of concurrent requests
     * & cache being regenerated while the other is being saved, it will
     * be cleared again after saving the new setting!
     */
    public function invalidateCache(): void
    {
        $this->cache->deleteItem(self::getCacheKey());
    }

    private static function getCacheKey(): string
    {
        return str_replace('\\', '_', self::class);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ModuleInstalledEvent::class => 'invalidateCache',
        ];
    }
}
