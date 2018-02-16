<?php

namespace ForkCMS\Component\Module;

use Common\Locale;
use Exception;

/**
 * The following things are mandatory when extending this class.
 *
 * You need to implement the method getModuleName.
 */
abstract class CopyModuleToOtherLocale implements CopyModuleToOtherLocaleInterface
{
    /** @var Locale */
    private $fromLocale;

    /** @var Locale */
    private $toLocale;

    /** @var array - Will be used to convert old ids to new ones if used in other places */
    private $idMap;

    /** @var array - Will be used to convert old module-extra ids to new ones if used in other places */
    private $moduleExtraIdMap;

    /** @var CopyModulesToOtherLocaleResults */
    private $previousResults;

    /** @var int */
    private $priority = 10;

    public function prepareForCopy(
        Locale $fromLocale,
        Locale $toLocale,
        CopyModulesToOtherLocaleResults $previousResults
    ): void {
        $this->fromLocale = $fromLocale;
        $this->toLocale = $toLocale;
        $this->previousResults = $previousResults;
        $this->idMap = [];
        $this->moduleExtraIdMap = [];
    }

    public function comparePriority(CopyModuleToOtherLocaleInterface $command): int
    {
        return $this->priority <=> $command->getPriority();
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
     * @param mixed $oldExtraId
     * @return mixed
     * @throws \Exception
     */
    public function getModuleExtraId($oldExtraId)
    {
        if (!array_key_exists($oldExtraId, $this->moduleExtraIdMap)) {
            throw new Exception('No new extra id found for the given old extra id.');
        }

        return $this->moduleExtraIdMap[$oldExtraId];
    }

    public function getModuleExtraIdMap(): array
    {
        return $this->moduleExtraIdMap;
    }

    abstract public function getModuleName(): string;

    public function getPreviousResults(): CopyModulesToOtherLocaleResults
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
     * @param mixed $oldId
     * @param mixed $newId
     */
    public function setModuleExtraId($oldId, $newId): void
    {
        $this->moduleExtraIdMap[$oldId] = $newId;
    }

    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }
}
