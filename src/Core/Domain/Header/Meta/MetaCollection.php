<?php

namespace ForkCMS\Core\Domain\Header\Meta;

use ForkCMS\Modules\Frontend\Domain\Meta\SEOFollow;
use ForkCMS\Modules\Frontend\Domain\Meta\SEOIndex;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class MetaCollection
{
    /** @var MetaData[] */
    private array $metaData = [];

    /** @var MetaLink[] */
    private array $metaLinks = [];

    /** @var MetaCustom[] */
    private array $metaCustoms = [];

    private SEOFollow $SEOFollow = SEOFollow::NONE;

    private SEOIndex $SEOIndex = SEOIndex::NONE;

    public function __construct(
        #[Autowire('%kernel.debug%')]
        private readonly bool $isDebug,
    ) {
    }

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

    public function setSEOFollow(SEOFollow $SEOFollow): void
    {
        if ($SEOFollow === SEOFollow::NONE) {
            return;
        }

        $this->SEOFollow = $SEOFollow;
    }

    public function setSEOIndex(SEOIndex $SEOIndex): void
    {
        if ($SEOIndex === SEOIndex::NONE) {
            return;
        }

        $this->SEOIndex = $SEOIndex;
    }

    /**
     * @param string $property The key (without og:).
     * @param string $openGraphData The value.
     * @param bool $overwrite Should we overwrite the previous value?
     */
    public function addOpenGraphData(string $property, string $openGraphData, bool $overwrite = false): void
    {
        $this->addMetaData(MetaData::forProperty('og:' . $property, $openGraphData), $overwrite);
    }

    public function addOpenGraphImage(string $image, bool $overwrite = false, int $width = 0, int $height = 0): void
    {
        $imageParts = parse_url($image);
        $isRelative = !array_key_exists('host', $imageParts);
        if ($isRelative) {
            $image = $_ENV['SITE_PROTOCOL'] . '://' . $_ENV['SITE_DOMAIN'] . $image;
        }

        $this->addMetaData(MetaData::forProperty('og:image', $image, ['property', 'content']), $overwrite);
        if (($isRelative && $_ENV['SITE_PROTOCOL'] === 'https') || ($imageParts['scheme'] ?? '') === 'https') {
            $this->addMetaData(
                MetaData::forProperty('og:image:secure_url', $image, ['property', 'content']),
                $overwrite
            );
        }

        if ($width !== 0) {
            $this->addMetaData(
                MetaData::forProperty('og:image:width', (string) $width, ['property', 'content']),
                $overwrite
            );
        }

        if ($height !== 0) {
            $this->addMetaData(
                MetaData::forProperty('og:image:height', (string) $height, ['property', 'content']),
                $overwrite
            );
        }
    }

    /**
     * Extract images from content that can be added add Open Graph image
     *
     * @param string $content The content (where from to extract the images).
     */
    public function extractOpenGraphImages(string $content): void
    {
        $images = [];

        // check if any img-tags are present in the content
        if (preg_match_all('/<img.*?src="(.*?)".*?\/>/i', $content, $images)) {
            // loop all found images and add to Open Graph metadata
            foreach ($images[1] as $image) {
                $this->addOpenGraphImage($image);
            }
        }
    }

    public function __toString(): string
    {
        $this->addRobotMeta();

        return trim(
            implode(
                "\n",
                [
                    implode("\n", $this->metaData),
                    implode("\n", $this->metaLinks),
                    implode("\n", $this->metaCustoms),
                ]
            )
        );
    }

    private function addRobotMeta(): void
    {
        if ($this->isDebug) {
            $this->SEOIndex = SEOIndex::NO_INDEX;
            $this->SEOFollow = SEOFollow::NO_FOLLOW;
        }

        $SEO = [];
        if ($this->SEOFollow !== SEOFollow::NONE) {
            $SEO[] = $this->SEOFollow->value;
        }
        if ($this->SEOIndex !== SEOIndex::NONE) {
            $SEO[] = $this->SEOIndex->value;
        }

        if (count($SEO) > 0) {
            $this->addMetaData(MetaData::forName('robots', implode(', ', $SEO)), true);
        }
    }
}
