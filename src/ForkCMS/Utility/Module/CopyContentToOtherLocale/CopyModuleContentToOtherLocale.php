<?php

namespace ForkCMS\Utility\Module\CopyContentToOtherLocale;

use Common\Locale;
use Exception;

/**
 * The following things are mandatory when extending this class.
 *
 * You need to implement the method getModuleName.
 */
abstract class CopyModuleContentToOtherLocale implements CopyModuleContentToOtherLocaleInterface
{
    /** @var Locale */
    private $fromLocale;

    /** @var Locale */
    private $toLocale;

    /** @var array - Will be used to convert old ids to new ones. Can be used in other places */
    private $idMap;

    /** @var array - Will be used to convert old module-extra ids to new ones. Can be used in other places */
    private $moduleExtraIdMap;

    /** @var Results */
    private $previousResults;

    /** @var int */
    private $priority = 10;

    public function prepareForCopy(
        Locale $fromLocale,
        Locale $toLocale,
        Results $previousResults
    ): void {
        $this->fromLocale = $fromLocale;
        $this->toLocale = $toLocale;
        $this->previousResults = $previousResults;
        $this->idMap = [];
        $this->moduleExtraIdMap = [];
    }

    public function getFromLocale(): Locale
    {
        return $this->fromLocale;
    }

    /**
     * @param mixed $oldId
     * @return mixed
     * @throws Exception
     */
    public function getId($oldId)
    {
        if (!array_key_exists($oldId, $this->idMap)) {
            throw new \Exception('No new id found for the given old id.');
        }

        return $this->moduleExtraIdMap[$oldId];
    }

    public function getIdMap(): array
    {
        return $this->idMap;
    }

    /**
     * @param mixed $oldModuleExtraId
     * @return mixed - New ModuleExtra id
     * @throws \Exception
     */
    public function getModuleExtraId($oldModuleExtraId)
    {
        if (!array_key_exists($oldModuleExtraId, $this->moduleExtraIdMap)) {
            throw new Exception('No new extra id found for the given old extra id.');
        }

        return $this->moduleExtraIdMap[$oldModuleExtraId];
    }

    public function getModuleExtraIdMap(): array
    {
        return $this->moduleExtraIdMap;
    }

    abstract public function getModuleName(): string;

    public function getPreviousResults(): Results
    {
        return $this->previousResults;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function getToLocale(): Locale
    {
        return $this->toLocale;
    }

    /**
     * @param mixed $oldId
     * @param mixed $newId
     */
    public function setId($oldId, $newId): void
    {
        $this->idMap[$oldId] = $newId;
    }

    /**
     * @param mixed $oldModuleExtraId
     * @param mixed $newModuleExtraId
     */
    public function setModuleExtraId($oldModuleExtraId, $newModuleExtraId): void
    {
        $this->moduleExtraIdMap[$oldModuleExtraId] = $newModuleExtraId;
    }

    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }
}
