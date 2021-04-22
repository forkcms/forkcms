<?php

namespace Backend\Modules\Faq\Domain\Category;

use Backend\Modules\Faq\Domain\Question\Question;
use Common\Doctrine\Entity\Meta;
use Common\Exception\CanNotSetExtraIdException;
use Common\Locale;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Backend\Modules\Faq\Domain\Category\CategoryRepository")
 * @ORM\Table(name="FaqCategory")
 */
class Category
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
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $extraId;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $sequence;

    /**
     * @var Collection<Question>
     *
     * @ORM\OneToMany(
     *     targetEntity="Backend\Modules\Faq\Domain\Question\Question",
     *     mappedBy="category",
     *     cascade={"remove"}
     * )
     */
    private $questions;

    public function __construct(Locale $locale, Meta $meta, string $title, int $sequence)
    {
        $this->locale = $locale;
        $this->meta = $meta;
        $this->title = $title;
        $this->sequence = $sequence;
        $this->questions = new ArrayCollection();
    }

    public function update(string $title, int $sequence): void
    {
        $this->title = $title;
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

    /**
     * @param int $extraId
     *
     * @throws CanNotSetExtraIdException
     */
    public function setExtraId(int $extraId): void
    {
        if ($this->extraId !== null) {
            throw new CanNotSetExtraIdException();
        }

        $this->extraId = $extraId;
    }

    public function getExtraId(): int
    {
        return $this->extraId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSequence(): int
    {
        return $this->sequence;
    }

    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'language' => $this->getLocale()->getLocale(),
            'meta_id' => $this->getMeta()->getId(),
            'extra_id' => $this->getExtraId(),
            'title' => $this->getTitle(),
            'sequence' => $this->getSequence(),
            'url' => $this->getMeta()->getUrl(),
        ];
    }
}
