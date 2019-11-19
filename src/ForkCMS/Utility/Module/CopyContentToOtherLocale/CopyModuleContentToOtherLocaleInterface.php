<?php

namespace ForkCMS\Utility\Module\CopyContentToOtherLocale;

use Backend\Modules\Pages\Domain\Page\Page;
use Common\Locale;
use Exception;

interface CopyModuleContentToOtherLocaleInterface
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

    public function getPreviousResults(): Results;

    public function getPriority(): int;

    public function getToLocale(): Locale;

    public function getPageToCopy(): ?Page;

    public function prepareForCopy(
        Locale $fromLocale,
        Locale $toLocale,
        ?Page $pageToCopy,
        Results $previousResults
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
