<?php

namespace Backend\Modules\Faq\Domain\Category;

use Common\Doctrine\Entity\Meta;
use Common\Locale;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="faq_categories")
 */
final class Category
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
     * @ORM\OneToOne(targetEntity="Common\Doctrine\Entity\Meta", cascade={"persist","remove"},  orphanRemoval=true)
     */
    private $meta;

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

    public function __construct(Locale $locale, Meta $meta, int $extraId, string $title, int $sequence)
    {
        $this->locale = $locale;
        $this->meta = $meta;
        $this->extraId = $extraId;
        $this->title = $title;
        $this->sequence = $sequence;
    }

    public function update(int $extraId, string $title, int $sequence): void
    {
        $this->extraId = $extraId;
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

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'language' => $this->getLocale()->getLocale(),
            'meta_id' => $this->getMeta()->getId(),
            'extra_id' => $this->getExtraId(),
            'title' => $this->getTitle(),
            'sequence' => $this->getSequence(),
        ];
    }
}
