<?php

namespace Backend\Modules\Pages\Domain\PageBlock;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Backend\Modules\Pages\Domain\PageBlock\PageBlockRepository")
 * @ORM\Table(name="PagesPageBlock")
 *
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
     * @var string
     *
     * @ORM\Column(type="page_block_type", name="extra_type", options={"default": "rich_text"})
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
     *     name="html",
     *     nullable=true,
     *     options={"comment": "if this block is HTML this field should contain the real HTML."}
     * )
     */
    private $html;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", name="visible", options={"default": "1"})
     */
    private $visible;

    /**
     * @var integer
     *
     * @ORM\Id()
     * @ORM\Column(type="integer", name="sequence")
     */
    private $sequence;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", name="created_on")
     */
    private $createdOn;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", name="edited_on")
     */
    private $editedOn;

    public function __construct(
        int $revisionId,
        string $position,
        ?int $extraId,
        ?PageBlockType $extraType,
        ?string $extraData,
        ?string $html,
        bool $visible,
        int $sequence
    ) {
        $this->revisionId = $revisionId;
        $this->position = $position;
        $this->extraId = $extraId;
        $this->extraType = $extraType ?? PageBlockType::richText();
        $this->extraData = $extraData;
        $this->html = $html;
        $this->visible = $visible;
        $this->sequence = $sequence;
    }

    public function update(
        int $revisionId,
        string $position,
        ?int $extraId,
        PageBlockType $extraType,
        ?string $extraData,
        ?string $html,
        bool $visible,
        int $sequence
    ): void {
        $this->revisionId = $revisionId;
        $this->position = $position;
        $this->extraId = $extraId;
        $this->extraType = $extraType;
        $this->extraData = $extraData;
        $this->html = $html;
        $this->visible = $visible;
        $this->sequence = $sequence;
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

    public function getExtraType(): PageBlockType
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

    public function getCreatedOn(): DateTime
    {
        return $this->createdOn;
    }

    public function getEditedOn(): DateTime
    {
        return $this->editedOn;
    }
}
