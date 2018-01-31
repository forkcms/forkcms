<?php

namespace ForkCMS\Component\Module;

final class CopyModulesToOtherLocaleResults
{
    /**
     * @var array
     */
    private $idMap;

    /**
     * @var array
     */
    private $extraIdMap;

    public function add(string $moduleName, array $idMap, array $extraIdMap): void
    {
        $this->idMap[$moduleName] = $idMap;
        $this->extraIdMap[$moduleName] = $extraIdMap;
    }

    public function getExtraId(string $moduleName, $id)
    {
        return $this->getValue($this->extraIdMap, $moduleName, $id);
    }

    public function getExtraIds(string $moduleName)
    {
        return $this->getValues($this->extraIdMap, $moduleName);
    }

    public function getId(string $moduleName, $id)
    {
        return $this->getValue($this->idMap, $moduleName, $id);
    }

    public function getIds(string $moduleName)
    {
        return $this->getValues($this->idMap, $moduleName);
    }

    private function getValue(array $map, string $moduleName, $id)
    {
        if (!array_key_exists($moduleName, $map)) {
            throw new \Exception(
                'The module "' . $moduleName . '" has not yet been copied or is not installed.
                 You should increase the priority, if you want it to be executed before this handler.
                 Then you can access eventual ids.'
            );
        }

        if (!array_key_exists($id, $map[$moduleName])) {
            throw new \Exception('The id doesn\'t exist in the map.');
        }

        return $map[$moduleName][$id];
    }

    private function getValues(array $map, string $moduleName): array
    {
        if (!array_key_exists($moduleName, $map)) {
            return [];
        }

        return $map[$moduleName];
    }

    public function hasModule(string $moduleName): bool
    {
        return array_key_exists($moduleName, $this->idMap) || array_key_exists($moduleName, $this->extraIdMap);
    }
}
