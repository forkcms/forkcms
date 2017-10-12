<?php

namespace Backend\Modules\SitemapGenerator\Tests\Domain;

use Backend\Modules\SitemapGenerator\Domain\SitemapItemChanged;
use PHPUnit\Framework\TestCase;

class SitemapItemChangedTest extends TestCase
{
    public function testConstructor(): void
    {
        $event = new SitemapItemChanged(
            'Blog',
            'BlogPost',
            23,
            'foo-bar',
            '/en/news/detail/foo-bar'
        );

        $this->assertSame(
            'Blog',
            $event->getModule()
        );
        $this->assertSame(
            'BlogPost',
            $event->getEntity()
        );
        $this->assertSame(
            23,
            $event->getId()
        );
        $this->assertSame(
            'foo-bar',
            $event->getSlug()
        );
        $this->assertSame(
            '/en/news/detail/foo-bar',
            $event->getUrl()
        );
    }
}
