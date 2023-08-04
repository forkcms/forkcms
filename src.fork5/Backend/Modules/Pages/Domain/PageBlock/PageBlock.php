<?php

namespace Backend\Modules\Pages\Domain\PageBlock;

use Backend\Modules\Pages\Domain\Page\Page;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="PagesPageBlock")
 * @ORM\Entity(repositoryClass="Backend\Modules\Pages\Domain\PageBlock\PageBlockRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class PageBlock
{
    /**
     * @var Page
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Backend\Modules\Pages\Domain\Page\Page", inversedBy="blocks")
     * @ORM\JoinColumn(name="revision_id", referencedColumnName="revision_id", onDelete="CASCADE")
     */
    private $page;

    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(type="string", name="position", length=33)
     *
     * @todo Position size must be limited due to limitation in mysql 5.5 regarding index size.
     * @todo This can be removed if Fork bumps the minimum mysql version
     */
    private $position;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", name="extra_id", nullable=true, options={"comment": "The linked extra."})
     */
    private $extraId;

    /**
     * @var Type
     *
     * @ORM\Column(type="pages_page_block_type", name="extra_type", options={"default": "rich_text"})
     */
    private $extraType;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", name="extra_data", nullable=true)
     */
    private $extraData;

    /**
     * @var string|null
     *
     * @ORM\Column(
     *     type="text",
     *     nullable=true,
     *     options={"comment": "if this block is HTML this field should contain the real HTML."}
     * )
     */
    private $html;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default": "1"})
     */
    private $visible;

    /**
     * @var int
     * @ORM\Id
     *
     * @ORM\Column(type="integer")
     */
    private $sequence;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $createdOn;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $editedOn;

    public function __construct(
        Page $page,
        string $position,
        ?int $extraId,
        ?Type $extraType,
        ?string $extraData,
        ?string $html,
        bool $visible,
        int $sequence
    ) {
        $this->page = $page;
        $this->position = $position;
        $this->extraId = $extraId;
        $this->extraType = $extraType ?? Type::richText();
        $this->extraData = $extraData;
        $this->html = $html;
        $this->visible = $visible;
        $this->sequence = $sequence;
    }

    public function getRevisionId(): int
    {
        return $this->page->getRevisionId();
    }

    public function getPage(): Page
    {
        return $this->page;
    }

    public function getPosition(): string
    {
        return $this->position;
    }

    public function getExtraId(): ?int
    {
        return $this->extraId;
    }

    public function getExtraType(): Type
    {
        return $this->extraType;
    }

    public function getExtraData(): ?string
    {
        return $this->extraData;
    }

    public function getHtml(): ?string
    {
        return $this->html;
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function getSequence(): int
    {
        return $this->sequence;
    }

    /**
     * @ORM\PrePersist()
     */
    public function setCreatedOn(): void
    {
        $this->createdOn = new DateTime();
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function setEditedOn(): void
    {
        $this->editedOn = new DateTime();
    }

    public function getCreatedOn(): DateTime
    {
        return $this->createdOn;
    }

    public function getEditedOn(): DateTime
    {
        return $this->editedOn;
    }

    public static function fromDataTransferObject(PageBlockDataTransferObject $dataTransferObject): self
    {
        return new self(
            $dataTransferObject->page,
            $dataTransferObject->position,
            $dataTransferObject->extraId,
            $dataTransferObject->extraType,
            $dataTransferObject->extraData,
            $dataTransferObject->html,
            $dataTransferObject->visible,
            $dataTransferObject->sequence
        );
    }
}
