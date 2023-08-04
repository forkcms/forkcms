<?php

namespace Backend\Modules\Faq\Domain\Question;

use Backend\Modules\Faq\Domain\Category\Category;
use Backend\Modules\Faq\Domain\Feedback\Feedback;
use Common\Doctrine\Entity\Meta;
use Common\Locale;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Backend\Modules\Faq\Domain\Question\QuestionRepository")
 * @ORM\Table(name="FaqQuestion")
 * @ORM\HasLifecycleCallbacks
 */
class Question
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Locale
     *
     * @ORM\Column(type="locale")
     */
    private $locale;

    /**
     * @var Meta
     *
     * @ORM\OneToOne(targetEntity="Common\Doctrine\Entity\Meta", cascade={"persist","remove"}, orphanRemoval=true)
     * @ORM\JoinColumn(
     *   name="meta_id",
     *   referencedColumnName="id",
     *   onDelete="cascade",
     *   nullable=false
     * )
     */
    private Meta $meta;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="Backend\Modules\Faq\Domain\Category\Category", inversedBy="questions")
     * @ORM\JoinColumn(
     *   name="category_id",
     *   referencedColumnName="id",
     *   onDelete="cascade",
     *   nullable=false
     * )
     */
    private Category $category;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $question;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $answer;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $createdOn;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $numberOfViews;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $numberOfUsefulYes;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $numberOfUsefulNo;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $hidden;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $sequence;

    /**
     * @var Collection<Feedback>
     *
     * @ORM\OneToMany(
     *     targetEntity="Backend\Modules\Faq\Domain\Feedback\Feedback",
     *     mappedBy="question",
     *     cascade={"remove"}
     * )
     */
    private $feedbackItems;

    public function __construct(
        Locale $locale,
        Meta $meta,
        Category $category,
        int $userId,
        string $question,
        string $answer,
        bool $hidden,
        int $sequence
    ) {
        $this->locale = $locale;
        $this->meta = $meta;
        $this->category = $category;
        $this->userId = $userId;
        $this->question = $question;
        $this->answer = $answer;
        $this->hidden = $hidden;
        $this->sequence = $sequence;
        $this->numberOfViews = 0;
        $this->numberOfUsefulYes = 0;
        $this->numberOfUsefulNo = 0;
    }

    public function update(
        Category $category,
        string $question,
        string $answer,
        bool $hidden,
        int $sequence
    ) {
        $this->category = $category;
        $this->question = $question;
        $this->answer = $answer;
        $this->hidden = $hidden;
        $this->sequence = $sequence;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getLocale(): Locale
    {
        return $this->locale;
    }

    public function getMeta(): Meta
    {
        return $this->meta;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getQuestion(): string
    {
        return $this->question;
    }

    public function getAnswer(): string
    {
        return $this->answer;
    }

    public function getCreatedOn(): DateTime
    {
        return $this->createdOn;
    }

    public function getNumberOfViews(): int
    {
        return $this->numberOfViews;
    }

    public function getNumberOfUsefulYes(): int
    {
        return $this->numberOfUsefulYes;
    }

    public function getNumberOfUsefulNo(): int
    {
        return $this->numberOfUsefulNo;
    }

    public function isHidden(): bool
    {
        return $this->hidden;
    }

    public function getSequence(): int
    {
        return $this->sequence;
    }

    public function getFeedbackItems(): Collection
    {
        return $this->feedbackItems;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist(): void
    {
        $this->createdOn = new DateTime();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'language' => $this->getLocale()->getLocale(),
            'meta_id' => $this->getMeta()->getId(),
            'category_id' => $this->getCategory()->getId(),
            'user_id' => $this->getUserId(),
            'question' => $this->getQuestion(),
            'answer' => $this->getAnswer(),
            'created_on' => $this->getCreatedOn(),
            'num_views' => $this->getNumberOfViews(),
            'num_useful_yes' => $this->getNumberOfUsefulYes(),
            'num_useful_no' => $this->getNumberOfUsefulNo(),
            'hidden' => (int) $this->isHidden(),
            'sequence' => $this->getSequence(),
            'url' => $this->getMeta()->getUrl(),
            'title' => $this->getQuestion(),
            'text' => $this->getAnswer(),
            'category_title' => $this->getCategory()->getTitle(),
            'category_url' => $this->getCategory()->getMeta()->getUrl(),
        ];
    }

    public function increaseViewCount(): void
    {
        $this->numberOfViews++;
    }

    public function increaseUsefulYesCount(): void
    {
        $this->numberOfUsefulYes++;
    }

    public function increaseUsefulNoCount(): void
    {
        $this->numberOfUsefulNo++;
    }

    public function decreaseUsefulYesCount(): void
    {
        $this->numberOfUsefulYes--;
    }

    public function decreaseUsefulNoCount(): void
    {
        $this->numberOfUsefulNo--;
    }
}
