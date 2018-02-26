<?php

namespace ForkCMS\Frontend\Core\Header;

final class MetaCollection
{
    /** @var MetaData[] */
    private $metaData = [];

    /** @var MetaLink[] */
    private $metaLinks = [];

    public function addMetaData(MetaData $metaData, bool $overwrite = false): void
    {
        if ($overwrite || !array_key_exists($metaData->getUniqueKey(), $this->metaData)) {
            $this->metaData[$metaData->getUniqueKey()] = $metaData;

            return;
        }

        // if we shouldn't combine we should ignore it since we are not overwriting the meta data
        if (!$metaData->shouldMergeOnDuplicateKey()) {
            return;
        }

        $this->metaData[$metaData->getUniqueKey()]->merge($metaData);
    }

    public function addMetaLink(MetaLink $metaLink, bool $overwrite = false): void
    {
        if ($overwrite || !array_key_exists($metaLink->getUniqueKey(), $this->metaLinks)) {
            $this->metaLinks[$metaLink->getUniqueKey()] = $metaLink;
        }
    }

    public function __toString(): string
    {
        return implode("\n", $this->metaData) . "\n" . implode("\n", $this->metaLinks);
    }
}
