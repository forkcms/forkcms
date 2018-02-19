<?php

namespace ForkCMS\Component\Module;

use Common\Locale;

interface CopyModuleToOtherLocaleInterface
{
    public function getFromLocale(): Locale;

    /**
     * @param mixed $oldId
     * @return mixed
     */
    public function getId($oldId);
    public function getIdMap(): array;

    /**
     * @param mixed $oldExtraId
     * @return mixed
     */
    public function getModuleExtraId($oldExtraId);
    public function getModuleExtraIdMap(): array;

    public function getModuleName(): string;
    public function getPreviousResults(): CopyModulesToOtherLocaleResults;
    public function getPriority(): int;
    public function getToLocale(): Locale;
    public function prepareForCopy(
        Locale $fromLocale,
        Locale $toLocale,
        CopyModulesToOtherLocaleResults $previousResults
    ): void;

    /**
     * @param mixed $oldId
     * @param $newId
     */
    public function setModuleExtraId($oldId, $newId): void;

    /**
     * @param $oldId
     * @param $newId
     */
    public function setId($oldId, $newId): void;
}
