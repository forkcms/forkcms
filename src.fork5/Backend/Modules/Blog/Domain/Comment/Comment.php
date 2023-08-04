<?php

namespace Backend\Modules\Blog\Domain\Comment;

use Common\Locale;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="blog_comments")
 * @ORM\Entity(repositoryClass="Backend\Modules\Blog\Domain\Comment\CommentRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Comment
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $postId;

    /**
     * @var Locale
     * @ORM\Column(type="locale")
     */
    private $locale;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $author;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $email;

    /**
     * @var null|string
     * @ORM\Column(type="string", nullable=true)
     */
    private $website;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $text;

    /**
     * @var string
     * @ORM\Column(type="string", options={"default":"comment"})
     */
    private $type;

    /**
     * @var string
     * @ORM\Column(type="string", options={"default":"moderation"})
     */
    private $status;

    /**
     * @var null|string
     * @ORM\Column(type="text", nullable=true)
     */
    private $data;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     */
    private $createdOn;

    public function __construct(
        int $postId,
        Locale $locale,
        string $author,
        string $email,
        string $text,
        string $type,
        string $status,
        ?string $website,
        ?string $data
    ) {
        $this->postId = $postId;
        $this->locale = $locale;
        $this->author = $author;
        $this->email = $email;
        $this->text = $text;
        $this->type = $type;
        $this->status = $status;
        $this->website = $website;
        $this->data = $data;
    }

    public function update(
        string $author,
        string $email,
        string $text,
        string $type,
        string $status,
        ?string $website,
        ?string $data
    ) {
        $this->author = $author;
        $this->email = $email;
        $this->text = $text;
        $this->type = $type;
        $this->status = $status;
        $this->website = $website;
        $this->data = $data;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPostId(): int
    {
        return $this->postId;
    }

    public function getLocale(): Locale
    {
        return $this->locale;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getData(): ?string
    {
        return $this->data;
    }

    public function getCreatedOn(): DateTime
    {
        return $this->createdOn;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist(): void
    {
        if ($this->createdOn === null) {
            $this->createdOn = new DateTime();
        }
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'post_id' => $this->postId,
            'language' => $this->locale->getLocale(),
            'created_on' => $this->createdOn->format('U'),
            'author' => $this->author,
            'email' => $this->email,
            'website' => $this->website,
            'text' => $this->text,
            'type' => $this->type,
            'status' => $this->status,
            'data' => $this->data,
        ];
    }
}
