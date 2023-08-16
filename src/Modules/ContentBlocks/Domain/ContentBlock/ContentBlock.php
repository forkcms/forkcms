<?php

namespace ForkCMS\Modules\ContentBlocks\Domain\ContentBlock;

use Doctrine\ORM\Mapping as ORM;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use DateTime;

#[ORM\Entity(repositoryClass: ContentBlockRepository::class)]
#[ORM\Table(name: 'contentblocks__contentblock')]
final class ContentBlock
{
    const DEFAULT_TEMPLATE = 'Default.html.twig';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'revision_id', type: 'integer')]
    private int $revisionId;

    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(name: 'user_id', type: 'integer')]
    private int $userId;

    #[ORM\Column(name: 'extra_id', type: 'integer')]
    private int $extraId;

    #[ORM\Column(type: 'string', length: 255, options: ['default' => 'Default.html.twig'])]
    private string $template;

    #[ORM\Column(name: 'language', type: 'string', length: 5, enumType: Locale::class)]
    private Locale $locale;

    #[ORM\Column(type: 'string', length: 255)]
    private string $title;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $text;

    #[ORM\Column(name: 'hidden', type: 'boolean', options: ['default' => false])]
    private bool $isHidden;

    #[ORM\Column(type: 'content_blocks_status', options: ['default' => 'active'])]
    private Status $status;

    #[ORM\Column(name: 'created_on', type: 'datetime')]
    private DateTime $createdOn;

    #[ORM\Column(name: 'edited_on', type: 'datetime')]
    private DateTime $editedOn;

    private function __construct()
    {
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

    /**
     * @return int
     */
    public function getExtraId(): int
    {
        return $this->extraId;
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
     * @return string|null
     */
    public function getText(): ?string
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

    /*public static function fromDataTransferObject(ContentBlockDataTransferObject $dataTransferObject): self
    {
        $entity = $dataTransferObject->isNew() ? new self() : $dataTransferObject->getEntity();

        return $entity;
    }*/
}
