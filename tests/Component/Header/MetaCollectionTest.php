<?php

namespace App\Tests\Component\Header;

use App\Component\Header\MetaCollection;
use App\Component\Header\MetaData;
use App\Component\Header\MetaLink;
use PHPUnit\Framework\TestCase;

class MetaCollectionTest extends TestCase
{
    public function testStringRepresentation(): void
    {
        $metaCollection = new MetaCollection();

        $metaCollection->addMetaData(MetaData::forName('description', 'lorem ipsum'));
        $metaCollection->addMetaLink(MetaLink::canonical('http://fork-cms.com'));

        $this->assertEquals(
            '<meta content="lorem ipsum" name="description">
<link href="http://fork-cms.com" rel="canonical">',
            (string) $metaCollection
        );
    }
}
