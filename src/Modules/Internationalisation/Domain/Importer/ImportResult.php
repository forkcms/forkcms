<?php

namespace ForkCMS\Modules\Internationalisation\Domain\Importer;

use ForkCMS\Modules\Internationalisation\Domain\Translation\Translation;

final class ImportResult
{
    private int $importedCount = 0;
    private int $updatedCount = 0;
    private int $skippedCount = 0;

    /** @var Translation[] */
    private array $failed = [];

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getUpdatedCount(): int
    {
        return $this->updatedCount;
    }

    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }

    public function getFailedCount(): int
    {
        return count($this->failed);
    }

    /** @return Translation[] */
    public function getFailed(): array
    {
        return $this->failed;
    }

    public function addFailed(Translation $translation): void
    {
        $this->failed[] = $translation;
    }

    public function addImported(): void
    {
        ++$this->importedCount;
    }

    public function addUpdated(): void
    {
        ++$this->updatedCount;
    }

    public function addSkipped(): void
    {
        ++$this->skippedCount;
    }

    public function getTotalCount(): int
    {
        return $this->getSkippedCount()
               + $this->getUpdatedCount()
               + $this->getFailedCount()
               + $this->getImportedCount();
    }
}
