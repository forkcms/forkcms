<?php

namespace Backend\Modules\ContentBlocks\ContentBlock;

use Backend\Core\Engine\Authentication;
use Backend\Core\Engine\Model;
use Backend\Core\Language\Locale;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="content_blocks")
 * @ORM\Entity(repositoryClass="Backend\Modules\ContentBlocks\ContentBlock\ContentBlockRepository")
 * @ORM\HasLifecycleCallbacks
 */
class ContentBlock
{
    const DEFAULT_TEMPLATE = 'Default.html.twig';

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
     * @var Locale
     *
     * @ORM\Column(type="locale", name="language")
     */
    private $locale;

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
     * @param string $locale
     * @param string $title
     * @param string $text
     * @param bool $isHidden
     * @param Status $status
     */
    private function __construct(
        $id,
        $userId,
        $extraId,
        $template,
        $locale,
        $title,
        $text,
        $isHidden,
        Status $status
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->extraId = $extraId;
        $this->template = $template;
        $this->locale = $locale;
        $this->title = $title;
        $this->text = $text;
        $this->isHidden = $isHidden;
        $this->status = $status;
    }

    /**
     * @param int $id
     * @param int $extraId The id of the module extra
     * @param string $locale
     * @param string $title
     * @param string $text
     * @param bool $isHidden
     * @param string $template
     *
     * @return self
     */
    public static function create(
        $id,
        $extraId,
        $locale,
        $title,
        $text,
        $isHidden,
        $template = self::DEFAULT_TEMPLATE
    ) {
        return new self(
            $id,
            Authentication::getUser()->getUserId(),
            $extraId,
            $template,
            $locale,
            $title,
            $text,
            $isHidden,
            Status::active()
        );
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
     * @return Locale
     */
    public function getLocale()
    {
        return $this->locale;
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
        $this->updateWidget();
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->editedOn = new DateTime();
        $this->updateWidget();
    }

    /**
     * Update the widget so it shows the correct title and has the correct template
     */
    private function updateWidget()
    {
        $editUrl = Model::createURLForAction('Edit', 'ContentBlocks', (string) $this->locale) . '&id=' . $this->id;

        // update data for the extra
        Model::updateExtra(
            $this->extraId,
            'data',
            [
                'id' => $this->id,
                'extra_label' => $this->title,
                'language' => (string) $this->locale,
                'edit_url' => $editUrl,
                'custom_template' => $this->template,
            ]
        );
    }
}
