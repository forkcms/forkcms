<?php

namespace Frontend\Core\Header;

final class MetaCollection
{
    /** @var MetaData[] */
    private $metaData = [];

    /** @var MetaLink[] */
    private $metaLinks = [];

    /**
     * @param MetaData $metaData
     * @param bool $overwrite
     */
    public function addMetaData(MetaData $metaData, bool $overwrite = false)
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

    /**
     * @param MetaLink $metaLink
     * @param bool $overwrite
     */
    public function addMetaLink(MetaLink $metaLink, bool $overwrite = false)
    {
        if ($overwrite || !array_key_exists($metaLink->getUniqueKey(), $this->metaLinks)) {
            $this->metaLinks[$metaLink->getUniqueKey()] = $metaLink;
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return implode("\n", $this->metaData) . "\n" . implode("\n", $this->metaLinks);
    }
}
