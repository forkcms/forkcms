<?php

namespace Backend\Modules\Sitemap\Manager;

use Backend\Core\Engine\Model;
use Backend\Modules\Sitemap\Domain\SitemapRowCollection\SitemapRowCollection;
use Exception;
use Backend\Modules\Sitemap\Provider\SitemapProviderInterface;

final class SitemapManager
{
    /** @var array */
    protected $providers = [];

    /** @var array */
    protected $cachedSitemapRowCollections = [];

    /** @var array */
    protected $entityClasses = [];

    public function addSitemapProvider(SitemapProviderInterface $sitemapProvider): void
    {
        $entityName = $this->getEntityName($sitemapProvider->getEntityClass());

        if ($this->exists($entityName)) {
            throw new \Exception('Sitemap provider for : "' . $entityName . '" already exists.');
        }

        $this->providers[$entityName] = $sitemapProvider;
        $this->entityClasses[$entityName] = $sitemapProvider->getEntityClass();
    }

    private function exists(string $entityName): bool
    {
        return array_key_exists($entityName, $this->providers);
    }

    public function existsEntityClass(string $entityClass): bool
    {
        return in_array($entityClass, $this->entityClasses);
    }

    public function getEntityClasses(): array
    {
        return $this->entityClasses;
    }

    public function getEntityName(string $entityClass): string
    {
        try {
            return (new \ReflectionClass($entityClass))->getShortName();
        } catch (\ReflectionException $e) {
            return $entityClass;
        }
    }

    public function getEntityNames(): array
    {
        return array_keys($this->providers);
    }

    public function getSitemapProvider(string $entityName): SitemapProviderInterface
    {
        if ($this->exists($entityName)) {
            return $this->providers[$entityName];
        }

        throw new Exception(
            'SitemapBundle can\'t find any defined SitemapProvider for the given storage type: "' . $entityName . '".'
        );
    }

    public function getSitemapRowCollection(string $language, string $entityName): SitemapRowCollection
    {
        if (!array_key_exists($entityName, $this->cachedSitemapRowCollections)) {
            $this->cachedSitemapRowCollections[$entityName] = $this->getSitemapProvider($entityName)->getRows($language);
        }

        Model::get('fork.settings')->set(
            'Sitemap',
            'last_modified_on_' . $language . '_' . $entityName,
            $this->cachedSitemapRowCollections[$entityName]->getLastModifiedOn()
        );

        return $this->cachedSitemapRowCollections[$entityName];
    }

    public function getSitemapRowCollectionLastModifiedOn(string $language, string $entityName): \DateTime
    {
        if (!array_key_exists($entityName, $this->cachedSitemapRowCollections)) {
            $lastModifiedOn = Model::get('fork.settings')->get(
                'Sitemap',
                'last_modified_on_' . $language . '_' . $entityName
            );

            if ($lastModifiedOn instanceof \DateTime) {
                return $lastModifiedOn;
            }

            return $this->getSitemapRowCollection($language, $entityName)->getLastModifiedOn();
        }

        return $this->cachedSitemapRowCollections[$entityName]->getLastModifiedOn();
    }
}
