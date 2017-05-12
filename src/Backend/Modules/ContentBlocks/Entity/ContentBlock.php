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
    private $template;

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

    public function getId(): int
    {
        return $this->id;
    }

    public function getRevisionId(): int
    {
        return $this->revisionId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getExtraId(): int
    {
        return $this->extraId;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function getLocale(): Locale
    {
        return $this->locale;
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

    public function getStatus(): ContentBlockStatus
    {
        return $this->status;
    }

    public function getCreatedOn(): DateTime
    {
        return $this->createdOn;
    }

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
