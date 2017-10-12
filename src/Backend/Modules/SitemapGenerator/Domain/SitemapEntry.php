<?php

namespace Backend\Modules\SitemapGenerator\Domain;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Backend\Modules\SitemapGenerator\Domain\SitemapEntryRepository")
 * @ORM\HasLifecycleCallbacks
 */
class SitemapEntry
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    private $module;

    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    private $entity;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $otherId;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $url;

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

    public function __construct(string $module, string $entity, int $otherId, string $slug, string $url)
    {
        $this->module = $module;
        $this->entity = $entity;
        $this->otherId = $otherId;
        $this->slug = $slug;
        $this->url = $url;
    }

    public static function fromEvent(SitemapItemChanged $event): self
    {
        return new self(
            $event->getModule(),
            $event->getEntity(),
            $event->getId(),
            $event->getSlug(),
            $event->getUrl()
        );
    }

    public function getModule(): string
    {
        return $this->module;
    }

    public function getEntity(): string
    {
        return $this->entity;
    }

    public function getOtherId(): int
    {
        return $this->otherId;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getUrl(): string
    {
        return $this->url;
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
     * @ORM\PreUpdate()
     */
    public function preUpdate()
    {
        $this->editedOn = new DateTime();
    }
}
