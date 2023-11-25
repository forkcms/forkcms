<?php

namespace ForkCMS\Core\Domain\Header\Meta;

final class MetaCollection
{
    /** @var MetaData[] */
    private array $metaData = [];

    /** @var MetaLink[] */
    private array $metaLinks = [];

    /** @var MetaCustom[] */
    private array $metaCustoms = [];

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

    public function addMetaCustom(MetaCustom $metaCustom, bool $overwrite = false): void
    {
        if ($overwrite || !array_key_exists($metaCustom->getUniqueKey(), $this->metaCustoms)) {
            $this->metaCustoms[$metaCustom->getUniqueKey()] = $metaCustom;
        }
    }

    public function addDescription(string $metaDescription, bool $overwrite = false): void
    {
        $this->addMetaData(MetaData::forName('description', $metaDescription), $overwrite);
    }

    public function addKeywords(string $metaKeywords, bool $overwrite = false): void
    {
        $this->addMetaData(MetaData::forName('keywords', $metaKeywords), $overwrite);
    }

    public function addRssLink(string $title, string $link): void
    {
        $this->addMetaLink(MetaLink::rss($link, $title), true);
    }

    /**
     * @param string $title The title (maximum 70 characters)
     * @param string $description A brief description of the card (maximum 200 characters)
     * @param string $imageUrl The URL of the image (minimum 280x150 and <1MB)
     * @param string $cardType The cardtype, possible types: https://dev.twitter.com/cards/types
     * @param string $siteHandle (optional)  Twitter handle of the site
     * @param string $creatorHandle (optional) Twitter handle of the author
     */
    public function setTwitterCard(
        string $title,
        string $description,
        string $imageUrl,
        string $cardType = 'summary',
        string $siteHandle = null,
        string $creatorHandle = null
    ): void {
        $this->addMetaData(MetaData::forName('twitter:card', $cardType));
        $this->addMetaData(MetaData::forName('twitter:title', $title));
        $this->addMetaData(MetaData::forName('twitter:description', $description));
        $this->addMetaData(MetaData::forName('twitter:image', $imageUrl));

        if ($siteHandle !== null) {
            $this->addMetaData(MetaData::forName('twitter:site', $siteHandle));
        }

        if ($creatorHandle !== null) {
            $this->addMetaData(MetaData::forName('twitter:creator', $creatorHandle));
        }
    }

    public function __toString(): string
    {
        return implode(
            "\n",
            [
                implode("\n", $this->metaData),
                implode("\n", $this->metaLinks),
                implode("\n", $this->metaCustoms),
            ]
        );
    }
}
