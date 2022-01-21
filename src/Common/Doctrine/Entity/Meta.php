<?php

namespace Common\Doctrine\Entity;

use Backend\Core\Engine\Meta as BackendMeta;
use Common\Doctrine\ValueObject\SEOFollow;
use Common\Doctrine\ValueObject\SEOIndex;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * @ORM\Table(name="meta", indexes={@ORM\Index(name="idx_url", columns={"url"})})
 * @ORM\Entity(repositoryClass="Common\Doctrine\Repository\MetaRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Meta implements JsonSerializable
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $keywords;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", name="keywords_overwrite", options={"default" = false})
     */
    private $keywordsOverwrite;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", name="description_overwrite", options={"default" = false})
     */
    private $descriptionOverwrite;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", name="title_overwrite", options={"default" = false})
     */
    private $titleOverwrite;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", name="url_overwrite", options={"default" = false})
     */
    private $urlOverwrite;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $custom;

    /**
     * @var array
     */
    private $unserialisedData;

    /**
     * @var string|null
     *
     * Only can be string during persisting or updating in the database as it then contains the serialised value
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $data;

    /**
     * @var SEOFollow|null
     *
     * @ORM\Column(type="seo_follow", name="seo_follow", nullable=true)
     */
    private $seoFollow;

    /**
     * @var SEOIndex|null
     *
     * @ORM\Column(type="seo_index", name="seo_index", nullable=true)
     */
    private $seoIndex;

    public function __construct(
        string $keywords,
        bool $keywordsOverwrite,
        string $description,
        bool $descriptionOverwrite,
        string $title,
        bool $titleOverwrite,
        string $url,
        bool $urlOverwrite,
        ?string $canonicalUrl,
        bool $canonicalUrlOverwrite,
        string $custom = null,
        SEOFollow $seoFollow = null,
        SEOIndex $seoIndex = null,
        array $unserialisedData = [],
        int $id = null
    ) {
        $this->keywords = $keywords;
        $this->keywordsOverwrite = $keywordsOverwrite;
        $this->description = $description;
        $this->descriptionOverwrite = $descriptionOverwrite;
        $this->title = $title;
        $this->titleOverwrite = $titleOverwrite;
        $this->url = $url;
        $this->urlOverwrite = $urlOverwrite;
        $this->custom = $custom;
        $this->unserialisedData = $unserialisedData;
        $this->seoFollow = $seoFollow;
        $this->seoIndex = $seoIndex;
        $this->id = $id;

        if ($canonicalUrlOverwrite) {
            $this->unserialisedData['canonical_url'] = $canonicalUrl;
            $this->unserialisedData['canonical_url_overwrite'] = $canonicalUrlOverwrite;
        } else {
            unset($this->unserialisedData['canonical_url']);
            unset($this->unserialisedData['canonical_url_overwrite']);
        }
    }

    public function update(
        string $keywords,
        bool $keywordsOverwrite,
        string $description,
        bool $descriptionOverwrite,
        string $title,
        bool $titleOverwrite,
        string $url,
        bool $urlOverwrite,
        ?string $canonicalUrl = null,
        bool $canonicalUrlOverwrite = false,
        string $custom = null,
        SEOFollow $seoFollow = null,
        SEOIndex $seoIndex = null,
        array $unserialisedData = []
    ) {
        $this->keywords = $keywords;
        $this->keywordsOverwrite = $keywordsOverwrite;
        $this->description = $description;
        $this->descriptionOverwrite = $descriptionOverwrite;
        $this->title = $title;
        $this->titleOverwrite = $titleOverwrite;
        $this->url = $url;
        $this->urlOverwrite = $urlOverwrite;
        $this->custom = $custom;
        $this->unserialisedData = $unserialisedData;
        $this->seoFollow = $seoFollow;
        $this->seoIndex = $seoIndex;

        if ($canonicalUrlOverwrite) {
            $this->unserialisedData['canonical_url'] = $canonicalUrl;
            $this->unserialisedData['canonical_url_overwrite'] = $canonicalUrlOverwrite;
        } else {
            unset($this->unserialisedData['canonical_url']);
            unset($this->unserialisedData['canonical_url_overwrite']);
        }
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function serialiseData()
    {
        if (!empty($this->unserialisedData)) {
            $this->data = serialize($this->unserialisedData);

            return;
        }

        $this->data = null;
    }

    /**
     * @ORM\PostPersist
     * @ORM\PostUpdate
     * @ORM\PostLoad
     */
    public function unSerialiseData()
    {
        if ($this->data === null) {
            $this->unserialisedData = [];

            return;
        }

        $this->unserialisedData = unserialize($this->data, ['allowed_classes' => false]);
    }

    public static function fromBackendMeta(BackendMeta $meta): self
    {
        $metaData = $meta->getData();

        return new self(
            $metaData['keywords'],
            $metaData['keywords_overwrite'],
            $metaData['description'],
            $metaData['description_overwrite'],
            $metaData['title'],
            $metaData['title_overwrite'],
            $metaData['url'],
            $metaData['url_overwrite'],
            $metaData['canonical_url'],
            $metaData['canonical_url_overwrite'],
            $metaData['custom'],
            array_key_exists('SEOFollow', $metaData) ? SEOFollow::fromString((string) $metaData['SEOFollow']) : null,
            array_key_exists('SEOIndex', $metaData) ? SEOIndex::fromString((string) $metaData['SEOIndex']) : null,
            $metaData['data'] ?? [],
            $meta->getId()
        );
    }

    /**
     * Used in the transformer of the Symfony form type for this entity
     *
     * @param array $metaData
     *
     * @return self
     */
    public static function updateWithFormData(array $metaData): self
    {
        return new self(
            $metaData['keywords'],
            $metaData['keywordsOverwrite'],
            $metaData['description'],
            $metaData['descriptionOverwrite'],
            $metaData['title'],
            $metaData['titleOverwrite'],
            $metaData['url'],
            $metaData['urlOverwrite'],
            $metaData['canonical_url'],
            $metaData['canonical_url_overwrite'],
            $metaData['custom'] ?? null,
            SEOFollow::fromString((string) $metaData['SEOFollow']),
            SEOIndex::fromString((string) $metaData['SEOIndex']),
            [],
            (int) $metaData['id']
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getKeywords(): string
    {
        return $this->keywords;
    }

    public function isKeywordsOverwrite(): bool
    {
        return $this->keywordsOverwrite;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function isDescriptionOverwrite(): bool
    {
        return $this->descriptionOverwrite;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function isTitleOverwrite(): bool
    {
        return $this->titleOverwrite;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function isUrlOverwrite(): bool
    {
        return $this->urlOverwrite;
    }

    public function getCanonicalUrl(): ?string
    {
        if ($this->unserialisedData === null) {
            return null;
        }

        if (array_key_exists('canonical_url', $this->unserialisedData)) {
            return $this->unserialisedData['canonical_url'];
        }

        return null;
    }

    public function isCanonicalUrlOverwrite(): bool
    {
        if ($this->unserialisedData === null) {
            return false;
        }

        if (array_key_exists('canonicalUrlOverwrite', $this->unserialisedData)) {
            return (bool) $this->unserialisedData['canonical_url_overwrite'];
        }

        return false;
    }

    public function getCustom(): ?string
    {
        return $this->custom;
    }

    public function getData(): array
    {
        return $this->unserialisedData;
    }

    public function hasSEOIndex(): bool
    {
        return $this->seoIndex instanceof SEOIndex && !$this->seoIndex->isNone();
    }

    public function getSEOIndex(): ?SEOIndex
    {
        if (!$this->hasSEOIndex()) {
            return null;
        }

        return $this->seoIndex;
    }

    public function hasSEOFollow(): bool
    {
        return $this->seoFollow instanceof SEOFollow && !$this->seoFollow->isNone();
    }

    public function getSEOFollow(): ?SEOFollow
    {
        if (!$this->hasSEOFollow()) {
            return null;
        }

        return $this->seoFollow;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'keywords' => $this->getKeywords(),
            'keywordsOverwrite' => $this->isKeywordsOverwrite(),
            'description' => $this->getDescription(),
            'descriptionOverwrite' => $this->isDescriptionOverwrite(),
            'title' => $this->getTitle(),
            'titleOverwrite' => $this->isTitleOverwrite(),
            'data' => $this->getData(),
            'url' => $this->getUrl(),
            'urlOverwrite' => $this->isUrlOverwrite(),
            'custom' => $this->getCustom(),
            'seoFollow' => $this->getSEOFollow(),
            'seoIndex' => $this->getSEOIndex(),
        ];
    }
}
