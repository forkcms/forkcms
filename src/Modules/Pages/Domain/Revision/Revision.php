<?php

namespace ForkCMS\Modules\Pages\Domain\Revision;

use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use ForkCMS\Core\Domain\Settings\EntityWithSettingsTrait;
use ForkCMS\Core\Domain\Settings\SettingsBag;
use ForkCMS\Modules\Backend\Domain\Action\ModuleAction;
use ForkCMS\Modules\Backend\Domain\User\Blameable;
use ForkCMS\Modules\Backend\Domain\User\User;
use ForkCMS\Modules\Extensions\Domain\Module\Module;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use ForkCMS\Modules\Extensions\Domain\ThemeTemplate\ThemeTemplate;
use ForkCMS\Modules\Frontend\Domain\Meta\EntityWithMetaTrait;
use ForkCMS\Modules\Frontend\Domain\Meta\Meta;
use ForkCMS\Modules\Frontend\Domain\Meta\SEOFollow;
use ForkCMS\Modules\Frontend\Domain\Meta\SEOIndex;
use ForkCMS\Modules\Internationalisation\Domain\Locale\EntityWithLocaleTrait;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use ForkCMS\Modules\Pages\Domain\Page\Page;
use ForkCMS\Modules\Pages\Domain\RevisionBlock\RevisionBlock;
use ForkCMS\Modules\Pages\Domain\RevisionBlock\RevisionBlockDataTransferObject;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Pageon\DoctrineDataGridBundle\Attribute\DataGrid;
use Pageon\DoctrineDataGridBundle\Attribute\DataGridActionColumn;
use Pageon\DoctrineDataGridBundle\Attribute\DataGridMethodColumn;
use Pageon\DoctrineDataGridBundle\Attribute\DataGridPropertyColumn;

/**
 * @Gedmo\SoftDeleteable(fieldName="isArchived", timeAware=true)
 */
#[ORM\Entity(repositoryClass: RevisionRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[DataGrid('Revision')]
#[DataGridActionColumn(
    route: 'backend_action',
    routeAttributes: [
        'module' => 'pages',
        'action' => 'page-add',
    ],
    routeAttributesCallback: [self::class, 'dataGridEditLinkCallback'],
    label: 'lbl.Copy',
    class: 'btn btn-default btn-sm',
    iconClass: 'fa fa-copy',
    requiredRole: ModuleAction::ROLE_PREFIX . 'PAGES__PAGE_ADD',
    columnAttributes: ['class' => 'fork-data-grid-action'],
)]
#[DataGridActionColumn(
    route: 'backend_action',
    routeAttributes: [
        'module' => 'pages',
        'action' => 'page-edit',
    ],
    routeAttributesCallback: [self::class, 'dataGridEditLinkCallback'],
    label: 'lbl.Edit',
    class: 'btn btn-primary btn-sm',
    iconClass: 'fa fa-edit',
    requiredRole: ModuleAction::ROLE_PREFIX . 'PAGES__PAGE_EDIT',
    columnAttributes: ['class' => 'fork-data-grid-action'],
)]
class Revision
{
    use EntityWithSettingsTrait;
    use EntityWithMetaTrait;
    use EntityWithLocaleTrait;
    use Blameable;

    #[ORM\ManyToOne(targetEntity: Page::class, cascade: ['persist'], inversedBy: 'revisions')]
    #[ORM\JoinColumn(nullable: false)]
    private Page $page;

    #[ORM\ManyToOne(targetEntity: Page::class, inversedBy: 'childRevisions')]
    private ?Page $parentPage;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: Types::STRING, enumType: MenuType::class)]
    private MenuType $type;

    #[ORM\Column(type: Types::STRING)]
    #[DataGridPropertyColumn(
        label: 'lbl.Title',
        route: 'backend_action',
        routeAttributes: [
            'module' => 'pages',
            'action' => 'page-edit',
        ],
        routeAttributesCallback: [self::class, 'dataGridEditLinkCallback'],
        routeRole: ModuleAction::ROLE_PREFIX . 'PAGES__PAGE_EDIT',
        columnAttributes: ['class' => 'title'],
    )]
    private string $title;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isDraft;

    #[ORM\ManyToOne(targetEntity: ThemeTemplate::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ThemeTemplate $themeTemplate;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private DateTimeImmutable|null $isArchived;

    /** @var Collection<array-key, RevisionBlock> */
    #[ORM\OneToMany(mappedBy: 'revision', targetEntity: RevisionBlock::class, cascade: ['persist'])]
    #[ORM\OrderBy(['sequence' => 'ASC'])]
    private Collection $blocks;

    /** @param Collection<string, non-empty-array<int, RevisionBlockDataTransferObject>> $blocks */
    private function __construct(
        Page $page,
        ?Page $parentPage,
        MenuType $type,
        string $title,
        bool $isDraft,
        ThemeTemplate $themeTemplate,
        ?DateTimeImmutable $isArchived,
        Collection $blocks,
        Meta $meta,
        Locale $locale,
        SettingsBag $settings,
    ) {
        $this->page = $page;
        $this->parentPage = $parentPage;
        $this->type = $type;
        $this->title = $title;
        $this->isDraft = $isDraft;
        $this->themeTemplate = $themeTemplate;
        $this->isArchived = $isArchived;
        /** @var Collection<array-key, RevisionBlock|RevisionBlockDataTransferObject[]> $blocks */
        $blocks->map(function (array $positionBlocks) use ($blocks): void {
            foreach ($positionBlocks as $block) {
                $block->revision = $this;
                $block->position = $blocks->key();
                $blocks->add(RevisionBlock::fromDataTransferObject($block));
            }
            $blocks->remove($blocks->key());
        });
        /** @var Collection<array-key, RevisionBlock> $blocks */
        $this->blocks = $blocks;
        $this->meta = $meta;
        $this->locale = $locale;
        $this->settings = $settings;

        if ($isDraft) {
            $this->archive();
        }
        $this->page->addRevision($this);
        $this->parentPage?->addChildRevision($this);
    }

    public static function fromDataTransferObject(RevisionDataTransferObject $revisionDataTransferObject): self
    {
        return new self(
            $revisionDataTransferObject->page,
            $revisionDataTransferObject->parentPage,
            $revisionDataTransferObject->type,
            $revisionDataTransferObject->title,
            $revisionDataTransferObject->isDraft,
            $revisionDataTransferObject->themeTemplate,
            $revisionDataTransferObject->isArchived,
            $revisionDataTransferObject->blocks,
            $revisionDataTransferObject->meta,
            $revisionDataTransferObject->locale,
            new SettingsBag($revisionDataTransferObject->settings),
        );
    }

    public function getPage(): Page
    {
        return $this->page;
    }

    public function getRel(): string
    {
        static $rel = null;
        if ($rel !== null) {
            return $rel;
        }

        $relParts = [];
        $follow = $this->meta->getSEOFollow();
        if ($follow === SEOFollow::noFollow) {
            $relParts[] = $follow->value;
        }
        $index = $this->meta->getSEOIndex();
        if ($index === SEOIndex::noIndex) {
            $relParts[] = $index->value;
        }

        $rel = implode(' ', $relParts);

        return $rel;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return 'test' . $this->locale->value;
    }

    public function isArchived(): ?DateTimeImmutable
    {
        return $this->isArchived;
    }

    public function isDraft(): bool
    {
        return $this->isDraft;
    }

    public function archive(): void
    {
        if ($this->isArchived === null) {
            $this->isArchived = new DateTimeImmutable();
        }
    }

    public function getParentPage(): ?Page
    {
        return $this->parentPage;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getLocale(): Locale
    {
        return $this->locale;
    }

    public function getMeta(): Meta
    {
        return $this->meta;
    }

    public function getSettings(): SettingsBag
    {
        return $this->settings;
    }

    public function getType(): MenuType
    {
        return $this->type;
    }

    public function getThemeTemplate(): ThemeTemplate
    {
        return $this->themeTemplate;
    }

    public function getArchivedDate(): ?DateTimeImmutable
    {
        return $this->isArchived;
    }

    /** @return Collection<array-key, RevisionBlock> */
    public function getBlocks(): Collection
    {
        return $this->blocks;
    }

    #[ORM\PrePersist]
    public function cleanupOldRevisions(PrePersistEventArgs $args): void
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $args->getObjectManager();
        $connection = $entityManager->getConnection();
        $entityManager->getFilters()->disable('softdeleteable');
        /** @var self[] $revisions */
        $revisions = $entityManager->getRepository(self::class)->findBy(['page' => $this->page], ['id' => 'DESC']);
        $counter = 0;
        $revisionClassMetadata = $entityManager->getClassMetadata(self::class);
        $revisionBlockClassMetadata = $entityManager->getClassMetadata(RevisionBlock::class);
        $moduleRepository = $entityManager->getRepository(Module::class);
        $maxRevisions = $moduleRepository
            ->findOneBy(['name' => ModuleName::fromFQCN($moduleRepository::class)])
            ->getSettings()
            ->getOr('max_revisions', 2);
        foreach ($revisions as $revision) {
            if ($revision->isArchived() === null || $revision->getLocale() !== $this->getLocale()) {
                continue;
            }
            if ($revision->isDraft()) {
                $connection->delete($revisionClassMetadata->getTableName(), ['id' => $revision->getId()]);
                continue;
            }
            ++$counter;
            if ($counter > $maxRevisions) {
                $connection->delete($revisionBlockClassMetadata->getTableName(), [
                    'id' => $revision->getBlocks()->map(
                        static function (RevisionBlock $revisionBlock): int {
                            return $revisionBlock->getId();
                        }
                    ),
                ]);
                $connection->delete($revisionClassMetadata->getTableName(), ['id' => $revision->getId()]);
            }
        }

        $entityManager->getFilters()->enable('softdeleteable');
    }

    public function __toString()
    {
        return $this->title;
    }

    public function getRouteName(): string
    {
        return Page::getRouteNameForIdAndLocale($this->page->getId(), $this->locale);
    }

    public function getNavigationTitle(): string
    {
        return $this->getSetting('navigationTitleOverwrite', false)
            ? $this->getSetting('navigationTitle') : $this->title;
    }

    /**
     * @param array{string?: string} $attributes
     *
     * @return array{string?: int|string}
     */
    public static function dataGridEditLinkCallback(self $revision, array $attributes): array
    {
        $attributes['slug'] = $revision->getPage()->getId();
        if ($revision->isArchived() !== null || $revision->isDraft()) {
            $attributes['revision'] = $revision->getId();
        }

        return $attributes;
    }

    #[DataGridMethodColumn(label: 'lbl.LastEdited')]
    public function getUpdatedOn(): DateTimeImmutable
    {
        return $this->updatedOn;
    }

    #[DataGridMethodColumn(label: 'lbl.By')]
    public function getUpdatedBy(): ?User
    {
        return $this->updatedBy;
    }
}
