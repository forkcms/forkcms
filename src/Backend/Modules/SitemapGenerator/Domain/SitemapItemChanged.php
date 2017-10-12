<?php

namespace Backend\Modules\SitemapGenerator\Domain;

use Symfony\Component\EventDispatcher\Event;

class SitemapItemChanged extends Event
{
    /** @var string */
    private $module;

    /** @var string */
    private $entity;

    /** @var int */
    private $id;

    /** @var string */
    private $slug;

    /** @var string */
    private $url;

    public function __construct(string $module, string $entity, int $id, string $slug, string$url)
    {
        $this->module = $module;
        $this->entity = $entity;
        $this->id = $id;
        $this->slug = $slug;
        $this->url = $url;
    }

    public function getModule(): string
    {
        return $this->module;
    }

    public function getEntity(): string
    {
        return $this->entity;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
