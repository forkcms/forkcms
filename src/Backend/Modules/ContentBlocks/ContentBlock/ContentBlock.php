<?php

namespace Backend\Modules\ContentBlocks\ContentBlock;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="content_blocks")
 * @ORM\Entity(repositoryClass="Backend\Modules\ContentBlocks\ContentBlock\ContentBlockRepository")
 * @ORM\HasLifecycleCallbacks
 */
class ContentBlock
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", name="revision_id")
     */
    private $revisionId;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", name="user_id")
     */
    private $userId;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", name="extra_id")
     */
    private $extraId;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, options={"default" = "Default.html.twig"})
     */
    private $template = 'Default.html.twig';

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=10)
     */
    private $language;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $text;

    /**
     * @var bool
     *
     * @ORM\Column(type="enum_bool", name="hidden", options={"default" = "N"})
     */
    private $isHidden;

    /**
     * @var Status
     *
     * @ORM\Column(type="content_blocks_status", options={"default" = "active"})
     */
    private $status;

    /**
     * @var Datetime
     *
     * @ORM\Column(type="datetime", name="created_on")
     */
    private $createdOn;

    /**
     * @var Datetime
     *
     * @ORM\Column(type="datetime", name="edited_on")
     */
    private $editedOn;

    /**
     * @param int $id
     * @param int $userId
     * @param int $extraId
     * @param string $template
     * @param string $language
     * @param string $title
     * @param string $text
     * @param bool $isHidden
     * @param Status $status
     */
    public function __construct(
        $id,
        $userId,
        $extraId,
        $template,
        $language,
        $title,
        $text,
        $isHidden,
        Status $status
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->extraId = $extraId;
        $this->template = $template;
        $this->language = $language;
        $this->title = $title;
        $this->text = $text;
        $this->isHidden = $isHidden;
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getRevisionId()
    {
        return $this->revisionId;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return int
     */
    public function getExtraId()
    {
        return $this->extraId;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return $this->isHidden;
    }

    /**
     * @return Status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * @return DateTime
     */
    public function getEditedOn()
    {
        return $this->editedOn;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->createdOn = $this->editedOn = new DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->editedOn = new DateTime();
    }
}
