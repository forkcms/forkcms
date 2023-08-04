<?php

namespace Backend\Modules\Blog\Domain\Category;

use Common\Doctrine\Entity\Meta;
use Common\Locale;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Backend\Modules\Blog\Domain\Category\CategoryRepository")
 * @ORM\Table(name="blog_categories")
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
     * @ORM\Column(type="locale")
     */
    private $locale;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $title;

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

    public function __construct(Locale $locale, string $title, Meta $meta)
    {
        $this->locale = $locale;
        $this->title = $title;
        $this->meta = $meta;
    }

    public function update(string $title, Meta $meta)
    {
        $this->title = $title;
        $this->meta = $meta;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getLocale(): Locale
    {
        return $this->locale;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getMeta(): Meta
    {
        return $this->meta;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'language' => $this->locale->getLocale(),
            'title' => $this->title,
            'meta_id' => $this->meta->getId(),
        ];
    }
}
