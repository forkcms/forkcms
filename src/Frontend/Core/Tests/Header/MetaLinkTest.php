<?php

namespace Frontend\Core\Tests\Header;

use Frontend\Core\Header\MetaLink;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class MetaLinkTest extends TestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testHrefCannotBeEmpty(): void
    {
        new MetaLink('', []);
    }

    public function testUniqueKey(): void
    {
        $metaLink = new MetaLink('http://fork-cms.com', ['title' => 'dummy']);

        $this->assertContains('dummy', $metaLink->getUniqueKey());
    }

    public function testCustomUniqueKey(): void
    {
        $metaLink = new MetaLink('http://fork-cms.com', ['title' => 'dummy'], ['href']);

        $this->assertContains('http://fork-cms.com', $metaLink->getUniqueKey());
    }

    public function testSearchingInAttributes(): void
    {
        $metaLink = new MetaLink('http://fork-cms.com', ['title' => 'dummy'], ['title']);

        $this->assertTrue($metaLink->hasAttributeWithValue('title', 'dummy'));
        $this->assertFalse($metaLink->hasAttributeWithValue('dummy', 'title'));
    }

    public function testCanonical(): void
    {
        $metaLink = MetaLink::canonical('http://fork-cms.com');

        $this->assertEquals('canonical', $metaLink->getUniqueKey());

        $this->assertTrue($metaLink->hasAttributeWithValue('rel', 'canonical'));
        $this->assertTrue($metaLink->hasAttributeWithValue('href', 'http://fork-cms.com'));
    }

    public function testRss(): void
    {
        $metaLink = MetaLink::rss('http://fork-cms.com', 'Fork CMS');

        $this->assertEquals('alternate|Fork CMS|application/rss+xml', $metaLink->getUniqueKey());

        $this->assertTrue($metaLink->hasAttributeWithValue('rel', 'alternate'));
        $this->assertTrue($metaLink->hasAttributeWithValue('type', 'application/rss+xml'));
        $this->assertTrue($metaLink->hasAttributeWithValue('href', 'http://fork-cms.com'));
    }

    public function testAlternateLanguage(): void
    {
        $metaLink = MetaLink::alternateLanguage('http://fork-cms.com', 'en');

        $this->assertEquals('en|alternate', $metaLink->getUniqueKey());

        $this->assertTrue($metaLink->hasAttributeWithValue('rel', 'alternate'));
        $this->assertTrue($metaLink->hasAttributeWithValue('hreflang', 'en'));
        $this->assertTrue($metaLink->hasAttributeWithValue('href', 'http://fork-cms.com'));
    }

    public function testNext(): void
    {
        $metaLink = MetaLink::next('http://fork-cms.com');

        $this->assertEquals('next', $metaLink->getUniqueKey());

        $this->assertTrue($metaLink->hasAttributeWithValue('rel', 'next'));
        $this->assertTrue($metaLink->hasAttributeWithValue('href', 'http://fork-cms.com'));
    }

    public function testPrevious(): void
    {
        $metaLink = MetaLink::previous('http://fork-cms.com');

        $this->assertEquals('prev', $metaLink->getUniqueKey());

        $this->assertTrue($metaLink->hasAttributeWithValue('rel', 'prev'));
        $this->assertTrue($metaLink->hasAttributeWithValue('href', 'http://fork-cms.com'));
    }

    public function testStringRepresentation(): void
    {
        $metaLink = MetaLink::canonical('http://fork-cms.com');

        $this->assertEquals('<link href="http://fork-cms.com" rel="canonical">', (string) $metaLink);
    }
}
