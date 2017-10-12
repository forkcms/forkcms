<?php

namespace Backend\Modules\SitemapGenerator\Tests\Domain;

use Backend\Modules\SitemapGenerator\Domain\SitemapEntry;
use Backend\Modules\SitemapGenerator\Domain\SitemapItemChanged;
use PHPUnit\Framework\TestCase;

class SitemapEntryTest extends TestCase
{
    /** @var SitemapItemChanged */
    protected $sitemapItemChangedEvent;

    /** @var SitemapEntry */
    protected $baseSitemapEntry;

    public function setUp(): void
    {
        $this->sitemapItemChangedEvent = new SitemapItemChanged(
            'Blog',
            'BlogPost',
            23,
            'foo-bar',
            '/en/news/detail/foo-bar'
        );

        $this->baseSitemapEntry = new SitemapEntry(
            'Blog',
            'BlogPost',
            23,
            'foo-bar',
            '/en/news/detail/foo-bar'
        );
    }

    public function testConstructor(): void
    {
        $this->assertSame(
            'Blog',
            $this->baseSitemapEntry->getModule()
        );
        $this->assertSame(
            'BlogPost',
            $this->baseSitemapEntry->getEntity()
        );
        $this->assertSame(
            23,
            $this->baseSitemapEntry->getOtherId()
        );
        $this->assertSame(
            'foo-bar',
            $this->baseSitemapEntry->getSlug()
        );
        $this->assertSame(
            '/en/news/detail/foo-bar',
            $this->baseSitemapEntry->getUrl()
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

    public function testUpdate():void
    {
        $this->baseSitemapEntry->update(
            'bar-foo',
            '/en/news/detail/bar-foo'
        );

        $this->assertSame(
            'Blog',
            $this->baseSitemapEntry->getModule()
        );
        $this->assertSame(
            'BlogPost',
            $this->baseSitemapEntry->getEntity()
        );
        $this->assertSame(
            23,
            $this->baseSitemapEntry->getOtherId()
        );
        $this->assertSame(
            'bar-foo',
            $this->baseSitemapEntry->getSlug()
        );
        $this->assertSame(
            '/en/news/detail/bar-foo',
            $this->baseSitemapEntry->getUrl()
        );
    }
}
