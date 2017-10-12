<?php

namespace Backend\Modules\SitemapGenerator\Tests\Domain;

use Backend\Modules\SitemapGenerator\Domain\SitemapEntry;
use Backend\Modules\SitemapGenerator\Domain\SitemapItemChanged;
use PHPUnit\Framework\TestCase;

class SitemapEntryTest extends TestCase
{
    /** @var SitemapItemChanged */
    protected $sitemapItemChangedEvent;

    public function setUp(): void
    {
        $this->sitemapItemChangedEvent = new SitemapItemChanged(
            'Blog',
            'BlogPost',
            23,
            'foo-bar',
            '/en/news/detail/foo-bar'
        );
    }

    public function testConstructor(): void
    {
        $sitemapEntry = new SitemapEntry(
            'Blog',
            'BlogPost',
            23,
            'foo-bar',
            '/en/news/detail/foo-bar'
        );

        $this->assertSame(
            'Blog',
            $sitemapEntry->getModule()
        );
        $this->assertSame(
            'BlogPost',
            $sitemapEntry->getEntity()
        );
        $this->assertSame(
            23,
            $sitemapEntry->getOtherId()
        );
        $this->assertSame(
            'foo-bar',
            $sitemapEntry->getSlug()
        );
        $this->assertSame(
            '/en/news/detail/foo-bar',
            $sitemapEntry->getUrl()
        );
    }

    public function testStaticCreator(): void
    {
        $sitemapEntry = SitemapEntry::fromEvent($this->sitemapItemChangedEvent);

        $this->assertSame(
            $this->sitemapItemChangedEvent->getModule(),
            $sitemapEntry->getModule()
        );
        $this->assertSame(
            $this->sitemapItemChangedEvent->getEntity(),
            $sitemapEntry->getEntity()
        );
        $this->assertSame(
            $this->sitemapItemChangedEvent->getId(),
            $sitemapEntry->getOtherId()
        );
        $this->assertSame(
            $this->sitemapItemChangedEvent->getSlug(),
            $sitemapEntry->getSlug()
        );
        $this->assertSame(
            $this->sitemapItemChangedEvent->getUrl(),
            $sitemapEntry->getUrl()
        );
    }
}
