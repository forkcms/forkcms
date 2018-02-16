<?php

namespace ForkCMS\Component\Module;

use Common\Locale;

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
    private $extraIdMap;

    /**
     * @var CopyModulesToOtherLocaleResults
     */
    private $previousResults;

    /** @var int */
    private $priority = 10;

    public function prepareForCopy(
        Locale $fromLocale,
        Locale $toLocale,
        CopyModulesToOtherLocaleResults $previousResults
    ) {
        $this->fromLocale = $fromLocale;
        $this->toLocale = $toLocale;
        $this->previousResults = $previousResults;
        $this->idMap = [];
        $this->extraIdMap = [];
    }

    public function compare(CopyModuleToOtherLocaleInterface $command)
    {
        return $this->priority <=> $command->getPriority();
    }

    public function getExtraId($oldExtraId)
    {
        if (!array_key_exists($oldExtraId, $this->extraIdMap)) {
            throw new \Exception('No new extra id found for the given old extra id.');
        }

        return $this->extraIdMap[$oldExtraId];
    }

    public function getExtraIdMap(): array
    {
        return $this->extraIdMap;
    }

    public function getFromLocale(): Locale
    {
        return $this->fromLocale;
    }

    public function getId($oldId)
    {
        if (!array_key_exists($oldId, $this->idMap)) {
            throw new \Exception('No new id found for the given old id.');
        }

        return $this->extraIdMap[$oldId];
    }

    public function getIdMap(): array
    {
        return $this->idMap;
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

    public function setExtraId($oldId, $newId): void
    {
        $this->extraIdMap[$oldId] = $newId;
    }

    public function setId($oldId, $newId): void
    {
        $this->idMap[$oldId] = $newId;
    }

    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }
}
