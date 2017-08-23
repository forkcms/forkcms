<?php

namespace Frontend\Core\Header;

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
        /* @remark Sumocoders staging websites should not be tracked */
        if (isset($_SERVER['HTTP_HOST']) && substr_count($_SERVER['HTTP_HOST'], '.sumocoders.eu') >= 1) {
            $this->addMetaData(
                MetaData::forName('robots', 'noindex, nofollow'),
                true
            );
        }

        return implode("\n", $this->metaData) . "\n" . implode("\n", $this->metaLinks);
    }
}
