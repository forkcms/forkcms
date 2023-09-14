<?php

namespace ForkCMS\Modules\ContentBlocks\Domain\ContentBlock;

use Doctrine\ORM\Mapping as ORM;
use ForkCMS\Modules\Backend\Domain\Action\ModuleAction;
use ForkCMS\Modules\Frontend\Domain\Block\Block;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use DateTime;
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
final class ContentBlock
{
    public const DEFAULT_TEMPLATE = 'Default.html.twig';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'revision_id', type: 'integer')]
    private int $revisionId;

    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(name: 'user_id', type: 'integer')]
    private int $userId;

    #[ORM\OneToOne(targetEntity: Block::class, cascade: ['persist'], fetch: 'EAGER')]
    private Block $widget;

    #[ORM\Column(type: 'string', options: ['default' => self::DEFAULT_TEMPLATE])]
    private string $template;

    #[ORM\Column(name: 'language', type: 'string', length: 5, enumType: Locale::class)]
    private Locale $locale;

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

    #[ORM\Column(type: 'string', enumType: Status::class, options: ['default' => 'active'])]
    private Status $status;

    #[ORM\Column(name: 'created_on', type: 'datetime')]
    private DateTime $createdOn;

    #[ORM\Column(name: 'edited_on', type: 'datetime')]
    private DateTime $editedOn;

    private function __construct(ContentBlockDataTransferObject $dataTransferObject)
    {
        $this->id = $dataTransferObject->id;
        $this->userId = $dataTransferObject->userId;
        $this->widget = $dataTransferObject->widget;
        $this->template = $dataTransferObject->template;
        $this->locale = $dataTransferObject->locale;
        $this->title = $dataTransferObject->title;
        $this->text = $dataTransferObject->text;
        $this->isHidden = !$dataTransferObject->isVisible;
        $this->status = $dataTransferObject->status;
    }

    /**
     * @return int
     */
    public function getRevisionId(): int
    {
        return $this->revisionId;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getWidget(): Block
    {
        return $this->widget;
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @return Locale
     */
    public function getLocale(): Locale
    {
        return $this->locale;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return bool
     */
    public function isHidden(): bool
    {
        return $this->isHidden;
    }

    #[DataGridMethodColumn(label: 'lbl.Visible')]
    public function isVisible(): string
    {
        return $this->isHidden ? 'lbl.No' : 'lbl.Yes';
    }

    /**
     * @return Status
     */
    public function getStatus(): Status
    {
        return $this->status;
    }

    /**
     * @return DateTime
     */
    public function getCreatedOn(): DateTime
    {
        return $this->createdOn;
    }

    /**
     * @return DateTime
     */
    public function getEditedOn(): DateTime
    {
        return $this->editedOn;
    }

    #[ORM\PrePersist]
    public function prePersist(): void
    {
        $this->createdOn = $this->editedOn = new DateTime();
    }

    public function archive(): void
    {
        $this->status = Status::Archived;
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
