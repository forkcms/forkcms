<?php

namespace Backend\Modules\Sitemap\Domain\SitemapRowCollection;

use Backend\Modules\Sitemap\Domain\SitemapRow\SitemapRow;
use Doctrine\Common\Collections\ArrayCollection;

class SitemapRowCollection extends ArrayCollection
{
    /** @var \DateTime|null */
    private $lastModifiedOn;

    public function add($element): bool
    {
        if (!$element instanceof SitemapRow) {
            throw new \Exception('The element you give must be an instance of ' . SitemapRow::class . '.');
        }

        $this->updateLastModifiedOn($element->getLastModifiedOn());

        return parent::add($element);
    }

    public function getLastModifiedOn(): \DateTime
    {
        if ($this->lastModifiedOn === null) {
            return new \DateTime();
        }

        return $this->lastModifiedOn;
    }

    private function updateLastModifiedOn(\DateTime $lastModifiedOn): void
    {
        if ($this->lastModifiedOn instanceof \DateTime && $this->lastModifiedOn->getTimestamp() > $lastModifiedOn->getTimestamp()) {
            return;
        }

        $this->lastModifiedOn = $lastModifiedOn;
    }
}
