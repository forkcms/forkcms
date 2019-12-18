<?php

namespace Backend\Modules\Pages\Domain\PageBlock;

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
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(
     *     type="integer",
     *     name="revision_id",
     *     options={"comment": "The ID of the page that contains this block."}
     * )
     */
    private $revisionId;

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
        int $revisionId,
        string $position,
        ?int $extraId,
        ?Type $extraType,
        ?string $extraData,
        ?string $html,
        bool $visible,
        int $sequence
    ) {
        $this->revisionId = $revisionId;
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
        return $this->revisionId;
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

    public function getTest(): string
    {
        return $this->test;
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
        if ($dataTransferObject->hasExistingPageBlock()) {
            $pageBlock = $dataTransferObject->getPageBlockEntity();
            $pageBlock->revisionId = $dataTransferObject->revisionId;
            $pageBlock->position = $dataTransferObject->position;
            $pageBlock->extraId = $dataTransferObject->extraId;
            $pageBlock->extraType = $dataTransferObject->extraType;
            $pageBlock->extraData = $dataTransferObject->extraData;
            $pageBlock->html = $dataTransferObject->html;
            $pageBlock->visible = $dataTransferObject->visible;
            $pageBlock->sequence = $dataTransferObject->sequence;

            return $pageBlock;
        }

        return new self(
            $dataTransferObject->revisionId,
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
