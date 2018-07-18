<?php

namespace Backend\Modules\Tags\Domain\Tag;

use Common\Locale;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Backend\Modules\Tags\Domain\Tag\TagRepository")
 * @ORM\Table(name="TagsTag")
 * @ORM\HasLifecycleCallbacks
 */
class Tag
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
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $tag;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $numberOfTimesLinked;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $url;

    public function __construct(Locale $locale, string $tag, string $url)
    {
        $this->locale = $locale;
        $this->tag = $tag;
        $this->numberOfTimesLinked = 0;
        $this->url = $url;
    }

    public function update(string $tag, string $url): void
    {
        $this->tag = $tag;
        $this->url = $url;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getLocale(): Locale
    {
        return $this->locale;
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    public function getNumberOfTimesLinked(): int
    {
        return $this->numberOfTimesLinked;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'language' => $this->getLocale()->getLocale(),
            'tag' => $this->tag,
            'number' => $this->numberOfTimesLinked,
            'url' => $this->url,
        ];
    }
}
