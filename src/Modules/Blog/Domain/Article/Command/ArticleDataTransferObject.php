<?php

namespace ForkCMS\Modules\Blog\Domain\Article\Command;

use ForkCMS\Modules\Backend\Domain\User\User;
use ForkCMS\Modules\Blog\Domain\Article\Article;
use ForkCMS\Modules\Blog\Domain\Article\Status;
use ForkCMS\Modules\Blog\Domain\Category\Category;
use ForkCMS\Modules\Frontend\Domain\Meta\Meta;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use DateTime;
use Symfony\Component\Validator\Constraints as Assert;

class ArticleDataTransferObject
{
    protected ?Article $blogPostEntity;

    public int $id;

    public int $revisionId;

    #[Assert\NotBlank()]
    public Category $category;

    public Locale $locale;

    #[Assert\NotBlank()]
    public string $title;

    #[Assert\NotBlank()]
    public string $introduction;

    #[Assert\NotBlank()]
    public string $text;

    public Status $status;

    public bool $hidden;

    #[Assert\NotBlank()]
    public DateTime $publishOn;

    public bool $allowComments;

    public int $numberOfComments = 0;

    public ?User $createdBy = null;

    public ?User $updatedBy = null;

    public ?Meta $meta = null;

    public function __construct(?Article $blogPostEntity = null)
    {
        $this->blogPostEntity = $blogPostEntity;

        if (!$blogPostEntity instanceof Article) {
            $this->publishOn = new DateTime();

            return;
        }

        $this->revisionId = $blogPostEntity->getRevisionId();
        $this->id = $blogPostEntity->getId();
        $this->category = $blogPostEntity->getCategory();
        $this->locale = $blogPostEntity->getLocale();
        $this->title = $blogPostEntity->getTitle();
        $this->introduction = $blogPostEntity->getIntroduction();
        $this->text = $blogPostEntity->getText();
        $this->status = $blogPostEntity->getStatus();
        $this->hidden = $blogPostEntity->isHidden();
        $this->allowComments = $blogPostEntity->isAllowComments();
        $this->numberOfComments = $blogPostEntity->getNumberOfComments();
        $this->createdBy = $blogPostEntity->getCreatedBy();
        $this->updatedBy = $blogPostEntity->getUpdatedBy();
        $this->publishOn = $blogPostEntity->getPublishOn();
    }

    public function hasEntity(): bool
    {
        return $this->blogPostEntity !== null;
    }

    public function getEntity(): ?Article
    {
        return $this->blogPostEntity;
    }
}
