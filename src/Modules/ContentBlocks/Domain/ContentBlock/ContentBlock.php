<?php

namespace ForkCMS\Modules\ContentBlocks\Domain\ContentBlock;

use Backend\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockDataTransferObject;
use Doctrine\ORM\Mapping as ORM;
use ForkCMS\Core\Domain\Settings\EntityWithSettingsTrait;
use ForkCMS\Modules\Backend\Domain\Action\ModuleAction;
use ForkCMS\Modules\Backend\Domain\User\Blameable;
use ForkCMS\Modules\Internationalisation\Domain\Locale\EntityWithLocaleTrait;
use DateTimeImmutable;
use ForkCMS\Modules\Frontend\Domain\Block\Block;
use Pageon\DoctrineDataGridBundle\Attribute\DataGrid;
use Pageon\DoctrineDataGridBundle\Attribute\DataGridActionColumn;
use Pageon\DoctrineDataGridBundle\Attribute\DataGridMethodColumn;
use Pageon\DoctrineDataGridBundle\Attribute\DataGridPropertyColumn;

#[ORM\Entity(repositoryClass: ContentBlockRepository::class)]
#[ORM\Table(name: 'contentblocks__contentblock')]
#[DataGrid('ContentBlock')]
#[ORM\HasLifecycleCallbacks]
#[DataGridActionColumn(
    route: 'backend_action',
    routeAttributes: [
        'module' => 'content_blocks',
        'action' => 'content_block_edit',
    ],
    routeAttributesCallback: [self::class, 'dataGridEditLinkCallback'],
    label: 'lbl.Edit',
    class: 'btn btn-primary btn-sm',
    iconClass: 'fa fa-edit',
    requiredRole: ModuleAction::ROLE_PREFIX . 'CONTENT_BLOCKS__CONTENT_BLOCK_EDIT',
    columnAttributes: ['class' => 'fork-data-grid-action'],
)]
class ContentBlock
{
    use Blameable;
    use EntityWithLocaleTrait;
    use EntityWithSettingsTrait;

    public const DEFAULT_TEMPLATE = 'Default.html.twig';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'revision_id', type: 'integer')]
    /** @phpstan-ignore-next-line */
    private int $revisionId;

    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\OneToOne(targetEntity: Block::class, cascade: ['persist'], fetch: 'EAGER')]
    private Block $widget;

    #[ORM\Column(type: 'string', options: ['default' => self::DEFAULT_TEMPLATE])]
    private string $template;

    #[ORM\Column(type: 'string')]
    #[DataGridPropertyColumn(
        sortable: true,
        label: 'lbl.Title',
        route: 'backend_action',
        routeAttributes: [
            'module' => 'content_blocks',
            'action' => 'content_block_edit',
        ],
        routeAttributesCallback: [self::class, 'dataGridEditLinkCallback'],
        routeRole: ModuleAction::ROLE_PREFIX . 'CONTENT_BLOCKS__CONTENT_BLOCK_EDIT',
        columnAttributes: ['class' => 'title'],
    )]
    private string $title;

    #[ORM\Column(type: 'text')]
    private string $text;

    #[ORM\Column(name: 'hidden', type: 'boolean', options: ['default' => false])]
    private bool $isHidden;

    #[ORM\Column(type: 'string', enumType: Status::class, options: ['default' => 'ACTIVE'])]
    private Status $status;

    private function __construct(ContentBlockDataTransferObject $dataTransferObject)
    {
        $this->id = $dataTransferObject->id;
        $this->widget = $dataTransferObject->widget;
        $this->template = $dataTransferObject->template;
        $this->locale = $dataTransferObject->locale;
        $this->title = $dataTransferObject->title;
        $this->text = $dataTransferObject->text;
        $this->isHidden = !$dataTransferObject->isVisible;
        $this->status = $dataTransferObject->status;
        $this->createdBy = $dataTransferObject->createdBy;
        $this->updatedBy  = $dataTransferObject->updatedBy;
        $this->settings = $dataTransferObject->settings;
    }

    public function getRevisionId(): int
    {
        return $this->revisionId;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getWidget(): Block
    {
        return $this->widget;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function isHidden(): bool
    {
        return $this->isHidden;
    }

    #[DataGridMethodColumn(label: 'lbl.Visible')]
    public function isVisible(): string
    {
        return $this->isHidden ? 'lbl.No' : 'lbl.Yes';
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    #[ORM\PrePersist]
    public function prePersist(): void
    {
        $this->createdOn = $this->updatedOn = new DateTimeImmutable();
    }

    public function archive(): void
    {
        $this->status = Status::Archived;
    }

    public function activate(): void
    {
        $this->status = Status::Active;
    }

    public static function fromDataTransferObject(ContentBlockDataTransferObject $dataTransferObject): self
    {
        return new self($dataTransferObject);
    }

    /**
     * @param array{string?: string} $attributes
     *
     * @return array{string?: int|string}
     */
    public static function dataGridEditLinkCallback(self $contentBlock, array $attributes): array
    {
        $attributes['slug'] = $contentBlock->getRevisionId();

        return $attributes;
    }
}
