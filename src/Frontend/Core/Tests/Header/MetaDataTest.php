<?php

namespace Frontend\Core\Tests\Header;

use Frontend\Core\Header\MetaData;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class MetaDataTest extends TestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testContentCannotBeEmpty(): void
    {
        new MetaData('', []);
    }

    public function testUniqueKey(): void
    {
        $metaData = new MetaData('lorem ipsum', ['name' => 'dummy']);

        $this->assertContains('lorem ipsum', $metaData->getUniqueKey());
    }

    public function testCustomUniqueKey(): void
    {
        $metaData = new MetaData('lorem ipsum', ['name' => 'dummy'], ['name']);

        $this->assertContains('dummy', $metaData->getUniqueKey());
    }

    public function testSearchingInAttributes(): void
    {
        $metaData = new MetaData('lorem ipsum', ['name' => 'dummy'], ['name']);

        $this->assertTrue($metaData->hasAttributeWithValue('name', 'dummy'));
        $this->assertFalse($metaData->hasAttributeWithValue('dummy', 'name'));
    }

    public function testShouldMergeOnDuplicateKey(): void
    {
        $this->assertTrue(MetaData::forName('description', 'lorem ipsum')->shouldMergeOnDuplicateKey());
        $this->assertTrue(MetaData::forName('keywords', 'lorem ipsum')->shouldMergeOnDuplicateKey());
        $this->assertTrue(MetaData::forName('robots', 'follow')->shouldMergeOnDuplicateKey());
        $this->assertFalse(MetaData::forName('title', 'lorem ipsum')->shouldMergeOnDuplicateKey());
    }

    public function testMerge(): void
    {
        $metaData = new MetaData('lorem', []);
        $metaData->merge(new MetaData('ipsum', []));

        $this->assertTrue($metaData->hasAttributeWithValue('content', 'lorem, ipsum'));
    }

    public function testForName(): void
    {
        $metaData = MetaData::forName('description', 'lorem ipsum');

        $this->assertEquals('description', $metaData->getUniqueKey());

        $this->assertTrue($metaData->hasAttributeWithValue('name', 'description'));
        $this->assertTrue($metaData->hasAttributeWithValue('content', 'lorem ipsum'));
    }

    public function testForProperty(): void
    {
        $metaData = MetaData::forProperty('description', 'lorem ipsum');

        $this->assertEquals('description', $metaData->getUniqueKey());

        $this->assertTrue($metaData->hasAttributeWithValue('property', 'description'));
        $this->assertTrue($metaData->hasAttributeWithValue('content', 'lorem ipsum'));
    }

    public function testStringRepresentation(): void
    {
        $metaData = MetaData::forName('description', 'lorem ipsum');

        $this->assertEquals('<meta content="lorem ipsum" name="description">', (string) $metaData);
    }
}
