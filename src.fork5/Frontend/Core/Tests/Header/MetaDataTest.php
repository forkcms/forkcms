<?php

namespace Frontend\Core\Tests\Header;

use Frontend\Core\Header\MetaData;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class MetaDataTest extends TestCase
{
    public function testContentCannotBeEmpty(): void
    {
        $this->expectExceptionMessage('The content can not be empty');
        new MetaData('', []);
    }

    public function testUniqueKey(): void
    {
        $metaData = new MetaData('lorem ipsum', ['name' => 'dummy']);

        self::assertStringContainsString('lorem ipsum', $metaData->getUniqueKey());
    }

    public function testCustomUniqueKey(): void
    {
        $metaData = new MetaData('lorem ipsum', ['name' => 'dummy'], ['name']);

        self::assertStringContainsString('dummy', $metaData->getUniqueKey());
    }

    public function testSearchingInAttributes(): void
    {
        $metaData = new MetaData('lorem ipsum', ['name' => 'dummy'], ['name']);

        self::assertTrue($metaData->hasAttributeWithValue('name', 'dummy'));
        self::assertFalse($metaData->hasAttributeWithValue('dummy', 'name'));
    }

    public function testShouldMergeOnDuplicateKey(): void
    {
        self::assertTrue(MetaData::forName('description', 'lorem ipsum')->shouldMergeOnDuplicateKey());
        self::assertTrue(MetaData::forName('keywords', 'lorem ipsum')->shouldMergeOnDuplicateKey());
        self::assertTrue(MetaData::forName('robots', 'follow')->shouldMergeOnDuplicateKey());
        self::assertFalse(MetaData::forName('title', 'lorem ipsum')->shouldMergeOnDuplicateKey());
    }

    public function testMerge(): void
    {
        $metaData = new MetaData('lorem', []);
        $metaData->merge(new MetaData('ipsum', []));

        self::assertTrue($metaData->hasAttributeWithValue('content', 'lorem, ipsum'));
    }

    public function testForName(): void
    {
        $metaData = MetaData::forName('description', 'lorem ipsum');

        self::assertEquals('description', $metaData->getUniqueKey());

        self::assertTrue($metaData->hasAttributeWithValue('name', 'description'));
        self::assertTrue($metaData->hasAttributeWithValue('content', 'lorem ipsum'));
    }

    public function testForProperty(): void
    {
        $metaData = MetaData::forProperty('description', 'lorem ipsum');

        self::assertEquals('description', $metaData->getUniqueKey());

        self::assertTrue($metaData->hasAttributeWithValue('property', 'description'));
        self::assertTrue($metaData->hasAttributeWithValue('content', 'lorem ipsum'));
    }

    public function testStringRepresentation(): void
    {
        $metaData = MetaData::forName('description', 'lorem ipsum');

        self::assertEquals('<meta content="lorem ipsum" name="description">', (string) $metaData);
    }
}
