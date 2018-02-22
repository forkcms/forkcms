<?php

namespace App\Tests\Frontend\Core\Header;

use App\Frontend\Core\Header\MetaCollection;
use App\Frontend\Core\Header\MetaData;
use App\Frontend\Core\Header\MetaLink;
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
