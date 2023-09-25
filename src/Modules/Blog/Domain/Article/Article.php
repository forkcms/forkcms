<?php

namespace ForkCMS\Modules\Blog\Domain\Article;

use Doctrine\ORM\Mapping as ORM;
use ForkCMS\Modules\Backend\Domain\User\Blameable;
use ForkCMS\Modules\Blog\Domain\Article\Command\ArticleDataTransferObject;
use ForkCMS\Modules\Blog\Domain\Category\Category;
use ForkCMS\Modules\Frontend\Domain\Meta\EntityWithMetaTrait;
use ForkCMS\Modules\Internationalisation\Domain\Locale\EntityWithLocaleTrait;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use Pageon\DoctrineDataGridBundle\Attribute\DataGrid;
use DateTime;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[ORM\Table(name: 'blog__articles')]
#[DataGrid('Article')]
#[ORM\HasLifecycleCallbacks]
final class Article
{
    use Blameable;
    use EntityWithMetaTrait;
    use EntityWithLocaleTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'revision_id', type: 'integer')]
    private int $revisionId;

    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'posts')]
    private Category $category;

    #[ORM\Column(type: 'string')]
    private string $title;

    #[ORM\Column(type: 'text')]
    private string $introduction;

    #[ORM\Column(type: 'text')]
    private string $text;

    // TODO image

    #[ORM\Column(type: 'string', enumType: Status::class, options: ['default' => 'draft'])]
    private Status $status;

    #[ORM\Column(type: 'boolean')]
    private bool $hidden;

    #[ORM\Column(type: 'boolean')]
    private bool $allowComments;

    #[ORM\Column(type: 'integer')]
    private int $numberOfComments;

    #[ORM\Column(type: 'datetime')]
    private DateTime $publishOn;

    private function __construct(ArticleDataTransferObject $dataTransferObject) {
        $this->id = $dataTransferObject->id;
        $this->category = $dataTransferObject->category;
        $this->locale = $dataTransferObject->locale;
        $this->title = $dataTransferObject->title;
        $this->introduction = $dataTransferObject->introduction;
        $this->text = $dataTransferObject->text;
        $this->status = $dataTransferObject->status;
        $this->hidden = $dataTransferObject->hidden;
        $this->publishOn = $dataTransferObject->publishOn;
        $this->allowComments = $dataTransferObject->allowComments;
        $this->numberOfComments = $dataTransferObject->numberOfComments;
        $this->createdBy = $dataTransferObject->createdBy;
        $this->updatedBy = $dataTransferObject->updatedBy;
        $this->meta = $dataTransferObject->meta;
    }

    public static function fromDataTransferObject(ArticleDataTransferObject $dataTransferObject): self
    {
        return new self($dataTransferObject);
    }

    public function getRevisionId(): int
    {
        return $this->revisionId;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function getLocale(): Locale
    {
        return $this->locale;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getIntroduction(): string
    {
        return $this->introduction;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function isHidden(): bool
    {
        return $this->hidden;
    }

    public function isAllowComments(): bool
    {
        return $this->allowComments;
    }

    public function getNumberOfComments(): int
    {
        return $this->numberOfComments;
    }

    public function getPublishOn(): DateTime
    {
        return $this->publishOn;
    }
}
