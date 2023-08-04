<?php

namespace Frontend\Core\Tests\Header;

use Frontend\Core\Header\MetaLink;
use PHPUnit\Framework\TestCase;

class MetaLinkTest extends TestCase
{
    public function testHrefCannotBeEmpty(): void
    {
        $this->expectExceptionMessage('The href can not be empty');
        new MetaLink('', []);
    }

    public function testUniqueKey(): void
    {
        $metaLink = new MetaLink('http://fork-cms.com', ['title' => 'dummy']);

        self::assertStringContainsString('dummy', $metaLink->getUniqueKey());
    }

    public function testCustomUniqueKey(): void
    {
        $metaLink = new MetaLink('http://fork-cms.com', ['title' => 'dummy'], ['href']);

        self::assertStringContainsString('http://fork-cms.com', $metaLink->getUniqueKey());
    }

    public function testSearchingInAttributes(): void
    {
        $metaLink = new MetaLink('http://fork-cms.com', ['title' => 'dummy'], ['title']);

        self::assertTrue($metaLink->hasAttributeWithValue('title', 'dummy'));
        self::assertFalse($metaLink->hasAttributeWithValue('dummy', 'title'));
    }

    public function testCanonical(): void
    {
        $metaLink = MetaLink::canonical('http://fork-cms.com');

        self::assertEquals('canonical', $metaLink->getUniqueKey());

        self::assertTrue($metaLink->hasAttributeWithValue('rel', 'canonical'));
        self::assertTrue($metaLink->hasAttributeWithValue('href', 'http://fork-cms.com'));
    }

    public function testRss(): void
    {
        $metaLink = MetaLink::rss('http://fork-cms.com', 'Fork CMS');

        self::assertEquals('alternate|Fork CMS|application/rss+xml', $metaLink->getUniqueKey());

        self::assertTrue($metaLink->hasAttributeWithValue('rel', 'alternate'));
        self::assertTrue($metaLink->hasAttributeWithValue('type', 'application/rss+xml'));
        self::assertTrue($metaLink->hasAttributeWithValue('href', 'http://fork-cms.com'));
    }

    public function testAlternateLanguage(): void
    {
        $metaLink = MetaLink::alternateLanguage('http://fork-cms.com', 'en');

        self::assertEquals('en|alternate', $metaLink->getUniqueKey());

        self::assertTrue($metaLink->hasAttributeWithValue('rel', 'alternate'));
        self::assertTrue($metaLink->hasAttributeWithValue('hreflang', 'en'));
        self::assertTrue($metaLink->hasAttributeWithValue('href', 'http://fork-cms.com'));
    }

    public function testNext(): void
    {
        $metaLink = MetaLink::next('http://fork-cms.com');

        self::assertEquals('next', $metaLink->getUniqueKey());

        self::assertTrue($metaLink->hasAttributeWithValue('rel', 'next'));
        self::assertTrue($metaLink->hasAttributeWithValue('href', 'http://fork-cms.com'));
    }

    public function testPrevious(): void
    {
        $metaLink = MetaLink::previous('http://fork-cms.com');

        self::assertEquals('prev', $metaLink->getUniqueKey());

        self::assertTrue($metaLink->hasAttributeWithValue('rel', 'prev'));
        self::assertTrue($metaLink->hasAttributeWithValue('href', 'http://fork-cms.com'));
    }

    public function testStringRepresentation(): void
    {
        $metaLink = MetaLink::canonical('http://fork-cms.com');

        self::assertEquals('<link href="http://fork-cms.com" rel="canonical">', (string) $metaLink);
    }
}
