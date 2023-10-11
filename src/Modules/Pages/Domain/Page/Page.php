<?php

namespace ForkCMS\Modules\Pages\Domain\Page;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ForkCMS\Core\Domain\Settings\EntityWithSettingsTrait;
use ForkCMS\Core\Domain\Settings\SettingsBag;
use ForkCMS\Modules\Frontend\Domain\Block\BlockName;
use ForkCMS\Modules\Frontend\Domain\Block\ModuleBlock;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use ForkCMS\Modules\Pages\Domain\Revision\Revision;
use ForkCMS\Modules\Pages\Domain\RevisionBlock\RevisionBlock;
use ForkCMS\Modules\Pages\Frontend\Widgets\Sitemap;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[ORM\Entity(repositoryClass: PageRepository::class)]
class Page
{
    use EntityWithSettingsTrait;

    public const PAGE_ID_HOME = 1;
    public const PAGE_ID_404 = 404;
    public const PAGE_ID_START = 1000;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: 'string', length: 5, enumType: Locale::class)]
    private Locale $originalLocale;

    /** @var Collection<array-key, Revision> */
    #[ORM\OneToMany(mappedBy: 'page', targetEntity: Revision::class, cascade: ['persist', 'remove'], fetch: 'EAGER')]
    private Collection $revisions;

    /** @var Collection<array-key, Revision> */
    #[ORM\OneToMany(mappedBy: 'parentPage', targetEntity: Revision::class)]
    private Collection $childRevisions;

    public function __construct(Locale $originalLocale)
    {
        $this->originalLocale = $originalLocale;
        $this->revisions = new ArrayCollection();
        $this->childRevisions = new ArrayCollection();
        $this->settings = new SettingsBag();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function hasId(): bool
    {
        return isset($this->id);
    }

    public function getOriginalLocale(): Locale
    {
        return $this->originalLocale;
    }

    public function addRevision(Revision $newRevision): void
    {
        $this->revisions->add($newRevision);

        if ($newRevision->isDraft()) {
            return;
        }

        if ($newRevision->isArchived() === null) {
            foreach ($this->revisions as $revision) {
                if ($revision !== $newRevision && $revision->getLocale() === $newRevision->getLocale()) {
                    $revision->archive();
                }
            }
        }
    }

    public function addChildRevision(Revision $newRevision): void
    {
        $this->childRevisions->add($newRevision);
    }

    public function getActiveRevision(Locale|null $locale = null): Revision
    {
        $locale ??= Locale::current();
        $expressionBuilder = Criteria::expr();

        $revision = $this->revisions->matching(
            Criteria::create()
                ->where($expressionBuilder->eq('locale', $locale))
                ->andWhere($expressionBuilder->eq('isDraft', false))
                ->andWhere($expressionBuilder->eq('isArchived', null))
        )->first();

        if ($revision instanceof Revision) {
            return $revision;
        }

        throw new NotFoundHttpException('Revision not found');
    }

    public function isHome(): bool
    {
        return $this->hasId() && $this->id === self::PAGE_ID_HOME;
    }

    public function is404(): bool
    {
        return $this->hasId() && $this->id === self::PAGE_ID_404;
    }

    public function isForbiddenToDelete(): bool
    {
        if ($this->getSetting('isForbiddenToDelete', false)) {
            return true;
        }

        return $this->hasId() && ($this->id === self::PAGE_ID_HOME || $this->id === self::PAGE_ID_404);
    }

    public function isForbiddenToMove(): bool
    {
        if ($this->getSetting('isForbiddenToMove', false)) {
            return true;
        }

        return $this->hasId() && ($this->id === self::PAGE_ID_HOME || $this->id === self::PAGE_ID_404);
    }

    public function isForbiddenToHaveChildren(): bool
    {
        if ($this->getSetting('isForbiddenToHaveChildren', false)) {
            return true;
        }

        return $this->hasId() && $this->id === self::PAGE_ID_404;
    }

    public function getPageTreeType(Locale $locale): string
    {
        $revision = $this->getActiveRevision($locale);
        if ($revision->getSetting('hidden', false)) {
            return 'hidden';
        }
        if ($this->getId() === self::PAGE_ID_HOME) {
            return 'home';
        }
        if ($this->getId() === self::PAGE_ID_404) {
            return 'error';
        }
        if (
            !$revision->getBlocks()->filter(
                static fn (RevisionBlock $block) => $block->getBlock()?->getBlock()->getFQCN() === Sitemap::class
            )->isEmpty()
        ) {
            return 'sitemap';
        }
        if (
            $revision->getSetting('internal_redirect', false)
            || $revision->getSetting('external_redirect', false)
        ) {
            return 'redirect';
        }
        if ($revision->getSetting('is_direct_action', false)) {
            return 'direct_action';
        }

        return 'page';
    }

    public static function getRouteNameForIdAndLocale(int $id, Locale|string $locale): string
    {
        return 'pages__page__' . $id . '.' . ($locale instanceof Locale ? $locale->value : $locale);
    }
}
