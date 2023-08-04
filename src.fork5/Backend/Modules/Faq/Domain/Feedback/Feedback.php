<?php

namespace Backend\Modules\Faq\Domain\Feedback;

use Backend\Modules\Faq\Domain\Question\Question;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Backend\Modules\Faq\Domain\Feedback\FeedbackRepository")
 * @ORM\Table(name="FaqFeedback")
 * @ORM\HasLifecycleCallbacks
 */
class Feedback
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
     * @var Question
     *
     * @ORM\ManyToOne(targetEntity="Backend\Modules\Faq\Domain\Question\Question", inversedBy="feedbackItems")
     * @ORM\JoinColumn(
     *   name="question_id",
     *   referencedColumnName="id",
     *   onDelete="cascade",
     *   nullable=false
     * )
     */
    private Question $question;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $text;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $processed;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $createdOn;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $editedOn;

    public function __construct(Question $question, string $text)
    {
        $this->question = $question;
        $this->text = $text;
        $this->processed = false;
    }

    public function update(Question $question, string $text, bool $processed)
    {
        $this->question = $question;
        $this->text = $text;
        $this->processed = $processed;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getQuestion(): Question
    {
        return $this->question;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function isProcessed(): bool
    {
        return $this->processed;
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
    public function prePersist(): void
    {
        $this->createdOn = $this->editedOn = new DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate(): void
    {
        $this->editedOn = new DateTime();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'question_id' => $this->getQuestion()->getId(),
            'question' => $this->getQuestion()->getQuestion(),
            'text' => $this->getText(),
            'processed' => $this->isProcessed(),
            'created_on' => $this->getCreatedOn(),
            'edited_on' => $this->getEditedOn(),
        ];
    }

    public function process()
    {
        $this->processed = true;
    }
}
