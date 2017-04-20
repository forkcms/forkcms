<?php

namespace Backend\Modules\ContentBlocks\Entity;

use Backend\Core\Engine\Model;
use Backend\Modules\ContentBlocks\ValueObject\ContentBlockStatus;
use Common\Locale;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="content_blocks")
 * @ORM\Entity(repositoryClass="Backend\Modules\ContentBlocks\Repository\ContentBlockRepository")
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
     * @var ContentBlockStatus
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
     * @param Locale $locale
     * @param string $title
     * @param string $text
     * @param bool $isHidden
     * @param ContentBlockStatus $status
     */
    private function __construct(
        int $id,
        int $userId,
        int $extraId,
        string $template,
        Locale $locale,
        string $title,
        string $text,
        bool $isHidden,
        ContentBlockStatus $status
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
     * @param int $userId
     * @param int $extraId The id of the module extra
     * @param Locale $locale
     * @param string $title
     * @param string $text
     * @param bool $isHidden
     * @param string $template
     *
     * @return self
     */
    public static function create(
        int $id,
        int $userId,
        int $extraId,
        Locale $locale,
        string $title,
        string $text,
        bool $isHidden,
        string $template = self::DEFAULT_TEMPLATE
    ) : self {
        return new self(
            $id,
            $userId,
            $extraId,
            $template,
            $locale,
            $title,
            $text,
            $isHidden,
            ContentBlockStatus::active()
        );
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
    public function getRevisionId(): int
    {
        return $this->revisionId;
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

    /**
     * @return ContentBlockStatus
     */
    public function getStatus(): ContentBlockStatus
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

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->createdOn = $this->editedOn = new DateTime();
    }

    /**
     * @ORM\PostPersist
     */
    public function postPersist()
    {
        $this->updateWidget();
    }

    /**
     * Update the widget so it shows the correct title and has the correct template
     */
    private function updateWidget()
    {
        $editUrl = Model::createURLForAction('Edit', 'ContentBlocks', (string) $this->locale) . '&id=' . $this->id;

        // update data for the extra
        // @TODO replace this with an implementation with doctrine
        $extras = Model::getExtras([$this->extraId]);
        $extra = reset($extras);
        $data = [
            'id' => $this->id,
            'language' => (string) $this->locale,
            'edit_url' => $editUrl,
        ];
        if (isset($extra['data'])) {
            $data = $data + (array) $extra['data'];
        }
        $data['custom_template'] = $this->template;
        $data['extra_label'] = $this->title;

        Model::updateExtra($this->extraId, 'data', $data);
    }

    /**
     * @param string $title
     * @param string $text
     * @param bool $isHidden
     * @param string $template
     * @param int $userId
     *
     * @return ContentBlock
     */
    public function update(string $title, string $text, bool $isHidden, string $template, int $userId): ContentBlock
    {
        $this->status = ContentBlockStatus::archived();

        return self::create(
            $this->id,
            $userId,
            $this->extraId,
            $this->locale,
            $title,
            $text,
            $isHidden,
            $template
        );
    }

    public function archive()
    {
        $this->status = ContentBlockStatus::archived();
    }
}
