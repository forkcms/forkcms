<?php

namespace Frontend\Core\Tests\Header;

use Frontend\Core\Header\MetaCollection;
use Frontend\Core\Header\MetaData;
use Frontend\Core\Header\MetaLink;

class MetaCollectionTest extends \PHPUnit_Framework_TestCase
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
