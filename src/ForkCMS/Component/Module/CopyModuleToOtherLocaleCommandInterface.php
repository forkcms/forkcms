<?php

namespace ForkCMS\Component\Module;

use Common\Locale;

interface CopyModuleToOtherLocaleCommandInterface
{
    public function compare(CopyModuleToOtherLocaleCommandInterface $command);
    public function getExtraId($oldExtraId);
    public function getExtraIdMap(): array;
    public function getId($oldId);
    public function getIdMap(): array;
    public function getFromLocale(): Locale;
    public function getModuleName(): string;
    public function getPreviousResults(): CopyModulesToOtherLocaleResults;
    public function getPriority(): int;
    public function getToLocale(): Locale;
    public function prepareForCopy(
        Locale $fromLocale,
        Locale $toLocale,
        CopyModulesToOtherLocaleResults $previousResults
    );
    public function setExtraId($oldId, $newId): void;
    public function setId($oldId, $newId): void;
}
