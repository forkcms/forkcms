<?php

namespace Common\Doctrine\Entity;

use Backend\Core\Engine\Meta as BackendMeta;
use Common\Doctrine\ValueObject\SEOFollow;
use Common\Doctrine\ValueObject\SEOIndex;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="meta", indexes={@ORM\Index(name="idx_url", columns={"url"})})
 * @ORM\Entity(repositoryClass="Common\Doctrine\Repository\MetaRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Meta
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
     * @ORM\Column(type="enum_bool", name="keywords_overwrite", options={"default" = "N"})
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
     * @ORM\Column(type="enum_bool", name="description_overwrite", options={"default" = "N"})
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
     * @ORM\Column(type="enum_bool", name="title_overwrite", options={"default" = "N"})
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
     * @ORM\Column(type="enum_bool", name="url_overwrite", options={"default" = "N"})
     */
    private $urlOverwrite;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $custom;

    /**
     * @var array
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $data;

    public function __construct(
        string $keywords,
        bool $keywordsOverwrite,
        string $description,
        bool $descriptionOverwrite,
        string $title,
        bool $titleOverwrite,
        string $url,
        bool $urlOverwrite,
        string $custom,
        array $data,
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
        $this->data = $data;
        $this->id = $id;
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
        string $custom,
        array $data
    ): void {
        $this->keywords = $keywords;
        $this->keywordsOverwrite = $keywordsOverwrite;
        $this->description = $description;
        $this->descriptionOverwrite = $descriptionOverwrite;
        $this->title = $title;
        $this->titleOverwrite = $titleOverwrite;
        $this->url = $url;
        $this->urlOverwrite = $urlOverwrite;
        $this->custom = $custom;
        $this->data = $data;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function serialiseData(): void
    {
        if (!empty($this->data)) {
            if (array_key_exists('seo_index', $this->data)) {
                $this->data['seo_index'] = (string) $this->data['seo_index'];
            }
            if (array_key_exists('seo_follow', $this->data)) {
                $this->data['seo_follow'] = (string) $this->data['seo_follow'];
            }
            $this->data = serialize($this->data);

            return;
        }

        $this->data = null;
    }

    /**
     * @ORM\PostPersist
     * @ORM\PostUpdate
     * @ORM\PostLoad
     */
    public function unSerialiseData(): void
    {
        if ($this->data === null) {
            $this->data = [];

            return;
        }

        // backwards compatible fix for when the seo is saved with the serialized value objects
        // @todo remove this for when all the modules use doctrine
        $this->data = preg_replace(
            '$O\\:3[67]\\:"Common\\\\Doctrine\\\\ValueObject\\\\(?:(?:SEOIndex)|(?:SEOFollow))"\\:1\\:{s\\:4[68]\\:"\\x00Common\\\\Doctrine\\\\ValueObject\\\\(?:(?:SEOIndex)|(?:SEOFollow))\\x00(?:(?:SEOIndex)|(?:SEOFollow))";(s\\:\d+\\:".+?";)}$',
            '$1',
            $this->data
        );
        $this->data = unserialize($this->data);
        if (array_key_exists('seo_index', $this->data)) {
            $this->data['seo_index'] = SEOIndex::fromString($this->data['seo_index']);
        }
        if (array_key_exists('seo_follow', $this->data)) {
            $this->data['seo_follow'] = SEOFollow::fromString($this->data['seo_follow']);
        }
    }

    public static function fromBackendMeta(BackendMeta $meta): self
    {
        $metaData = $meta->getData();

        return new self(
            $metaData['keywords'],
            $metaData['keywords_overwrite'] === 'Y',
            $metaData['description'],
            $metaData['description_overwrite'] === 'Y',
            $metaData['title'],
            $metaData['title_overwrite'] === 'Y',
            $metaData['url'],
            $metaData['url_overwrite'] === 'Y',
            $metaData['custom'],
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
            $metaData['custom'] ?? null,
            [
                'seo_index' => SEOIndex::fromString($metaData['SEOIndex']),
                'seo_follow' => SEOFollow::fromString($metaData['SEOFollow']),
            ],
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

    public function getCustom(): string
    {
        return $this->custom;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function hasSEOIndex(): bool
    {
        return array_key_exists('seo_index', $this->data)
               && !SEOIndex::fromString($this->data['seo_index'])->isNone();
    }

    public function getSEOIndex(): ?SEOIndex
    {
        if (!$this->hasSEOIndex()) {
            return null;
        }

        return SEOIndex::fromString($this->data['seo_index']);
    }

    public function hasSEOFollow(): bool
    {
        return array_key_exists('seo_follow', $this->data)
               && !SEOFollow::fromString($this->data['seo_follow'])->isNone();
    }

    public function getSEOFollow(): ?SEOFollow
    {
        if (!$this->hasSEOFollow()) {
            return null;
        }

        return SEOFollow::fromString($this->data['seo_follow']);
    }
}
