<?php

namespace ForkCMS\Modules\Frontend\Domain\Meta;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ForkCMS\Core\Domain\Settings\EntityWithSettingsTrait;
use ForkCMS\Core\Domain\Settings\SettingsBag;
use ForkCMS\Modules\Backend\Domain\User\Blameable;
use Gedmo\Sluggable\Util\Urlizer;
use JsonSerializable;

#[ORM\Entity(repositoryClass: MetaRepository::class)]
#[ORM\Index(columns: ['slug'], name: 'idx_slug')]
#[ORM\HasLifecycleCallbacks]
class Meta implements JsonSerializable
{
    use EntityWithSettingsTrait;

    use Blameable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: Types::STRING)]
    private string $keywords;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $keywordsOverwrite;

    #[ORM\Column(type: Types::STRING)]
    private string $description;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $descriptionOverwrite;

    #[ORM\Column(type: Types::STRING)]
    private string $title;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $titleOverwrite;

    #[ORM\Column(type: Types::STRING)]
    private string $slug;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $slugOverwrite;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private string|null $canonicalUrl;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $canonicalUrlOverwrite;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private string|null $custom;

    #[ORM\Column(type: SEOFollowDBALType::NAME)]
    private SEOFollow $seoFollow;

    #[ORM\Column(type: SEOIndexDBALType::NAME)]
    private SEOIndex $seoIndex;

    public function __construct(
        string $keywords,
        bool $keywordsOverwrite,
        string $description,
        bool $descriptionOverwrite,
        string $title,
        bool $titleOverwrite,
        string $slug,
        bool $slugOverwrite,
        ?string $canonicalUrl = null,
        bool $canonicalUrlOverwrite = false,
        string $custom = null,
        SEOFollow $seoFollow = null,
        SEOIndex $seoIndex = null,
        SettingsBag $settings = null,
    ) {
        $this->settings = $settings ?? new SettingsBag();
        $this->update(...func_get_args());
    }

    public function update(
        string $keywords,
        bool $keywordsOverwrite,
        string $description,
        bool $descriptionOverwrite,
        string $title,
        bool $titleOverwrite,
        string $slug,
        bool $slugOverwrite,
        ?string $canonicalUrl = null,
        bool $canonicalUrlOverwrite = false,
        string $custom = null,
        SEOFollow $seoFollow = null,
        SEOIndex $seoIndex = null,
        SettingsBag $settings = null,
    ): void {
        $this->keywords = $keywords;
        $this->keywordsOverwrite = $keywordsOverwrite;
        $this->description = $description;
        $this->descriptionOverwrite = $descriptionOverwrite;
        $this->title = $title;
        $this->titleOverwrite = $titleOverwrite;
        $this->slug = $slug;
        $this->slugOverwrite = $slugOverwrite;
        $this->custom = $custom;
        $this->seoFollow = $seoFollow ?? SEOFollow::none;
        $this->seoIndex = $seoIndex ?? SEOIndex::none;
        $this->canonicalUrl = $canonicalUrl;
        $this->canonicalUrlOverwrite = $canonicalUrlOverwrite;
        $this->settings = $settings ?? $this->settings;
    }

    public static function forName(string $title): self
    {
        return new self(
            $title,
            false,
            $title,
            false,
            $title,
            false,
            Urlizer::urlize($title),
            false,
            null,
            false,
            null,
            null,
            null,
            null
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

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function isSlugOverwrite(): bool
    {
        return $this->slugOverwrite;
    }

    public function getCanonicalUrl(): ?string
    {
        return $this->canonicalUrl;
    }

    public function isCanonicalUrlOverwrite(): bool
    {
        return $this->canonicalUrlOverwrite;
    }

    public function getCustom(): ?string
    {
        return $this->custom;
    }

    public function getSEOIndex(): SEOIndex
    {
        return $this->seoIndex;
    }

    public function getSEOFollow(): SEOFollow
    {
        return $this->seoFollow;
    }

    /** @return array<string, mixed> */
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
            'settings' => $this->getSettings(),
            'slug' => $this->getSlug(),
            'slugOverwrite' => $this->isSlugOverwrite(),
            'custom' => $this->getCustom(),
            'seoFollow' => $this->getSEOFollow(),
            'seoIndex' => $this->getSEOIndex(),
        ];
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }
}
