<?php

namespace ForkCMS\Utility\Module\CopyContentToOtherLocale;

use ForkCMS\Utility\Module\CopyContentToOtherLocale\Exception\CopiedModuleContentToOtherLocaleNotFound;

final class Results
{
    /** @var array */
    private $idMap;

    /** @var array */
    private $moduleExtraIdMap;

    public function add(string $moduleName, array $idMap, array $moduleExtraIdMap): void
    {
        $this->idMap[$moduleName] = $idMap;
        $this->moduleExtraIdMap[$moduleName] = $moduleExtraIdMap;
    }

    /**
     * @param string $moduleName
     * @param mixed $id
     * @return mixed - The new id
     * @throws \Exception
     */
    public function getModuleExtraId(string $moduleName, $id)
    {
        return $this->getNewId($this->moduleExtraIdMap, $moduleName, $id);
    }

    public function getModuleExtraIds(string $moduleName): array
    {
        return $this->getNewIds($this->moduleExtraIdMap, $moduleName);
    }

    /**
     * @param string $moduleName
     * @param mixed $id
     * @return mixed - The new id
     * @throws \Exception
     */
    public function getId(string $moduleName, $id)
    {
        return $this->getNewId($this->idMap, $moduleName, $id);
    }

    public function getIds(string $moduleName): array
    {
        return $this->getNewIds($this->idMap, $moduleName);
    }

    /**
     * @param array $map
     * @param string $moduleName
     * @param mixed $id
     * @return mixed - The new id
     * @throws \Exception
     */
    private function getNewId(array $map, string $moduleName, $id)
    {
        if (!array_key_exists($moduleName, $map)) {
            throw CopiedModuleContentToOtherLocaleNotFound::forModule($moduleName);
        }

        if (!array_key_exists($id, $map[$moduleName])) {
            throw CopiedModuleContentToOtherLocaleNotFound::forId($moduleName, $id);
        }

        return $map[$moduleName][$id];
    }

    private function getNewIds(array $map, string $moduleName): array
    {
        if (!array_key_exists($moduleName, $map)) {
            return [];
        }

        return $map[$moduleName];
    }

    public function hasModule(string $moduleName): bool
    {
        return array_key_exists($moduleName, $this->idMap) || array_key_exists($moduleName, $this->moduleExtraIdMap);
    }
}
