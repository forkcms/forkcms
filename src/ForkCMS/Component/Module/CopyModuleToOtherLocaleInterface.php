<?php

namespace ForkCMS\Component\Module;

use Common\Locale;
use Exception;

interface CopyModuleToOtherLocaleInterface
{
    public function getFromLocale(): Locale;

    /**
     * @param mixed $oldId
     * @return mixed
     * @throws Exception
     */
    public function getId($oldId);

    public function getIdMap(): array;

    /**
     * @param mixed $oldModuleExtraId
     * @return mixed - New ModuleExtra id
     * @throws Exception
     */
    public function getModuleExtraId($oldModuleExtraId);

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
     * @param mixed $oldModuleExtraId
     * @param mixed $newModuleExtraId
     */
    public function setModuleExtraId($oldModuleExtraId, $newModuleExtraId): void;

    /**
     * @param mixed $oldId - Old ModuleExtra id
     * @param mixed $newId - New ModuleExtra id
     */
    public function setId($oldId, $newId): void;
}
