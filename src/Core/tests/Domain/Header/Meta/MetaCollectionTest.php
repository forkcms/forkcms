<?php

namespace ForkCMS\Core\tests\Domain\Header\Meta;

use ForkCMS\Core\Domain\Header\Meta\MetaCollection;
use PHPUnit\Framework\TestCase;

class MetaCollectionTest extends TestCase
{
    public function testAddOpenGraphImage(): void
    {
        $imageUrls = [
            '/image.jpg' => '<meta content="https://fork.test/image.jpg" property="og:image">
<meta content="https://fork.test/image.jpg" property="og:image:secure_url">',
            '/image.jpg?foo=bar' => '<meta content="https://fork.test/image.jpg?foo=bar" property="og:image">
<meta content="https://fork.test/image.jpg?foo=bar" property="og:image:secure_url">',
            'http://fork.test/image.jpg' => '<meta content="http://fork.test/image.jpg" property="og:image">',
            'https://fork.test/image.jpg' => '<meta content="https://fork.test/image.jpg" property="og:image">
<meta content="https://fork.test/image.jpg" property="og:image:secure_url">',
            'http://fork.test/image.jpg?foo=bar' => '<meta content="http://fork.test/image.jpg?foo=bar" property="og:image">',
            'https://fork.test/image.jpg?foo=bar' => '<meta content="https://fork.test/image.jpg?foo=bar" property="og:image">
<meta content="https://fork.test/image.jpg?foo=bar" property="og:image:secure_url">',
            'http://fork-cms.com/image.jpg' => '<meta content="http://fork-cms.com/image.jpg" property="og:image">',
            'https://fork-cms.com/image.jpg' => '<meta content="https://fork-cms.com/image.jpg" property="og:image">
<meta content="https://fork-cms.com/image.jpg" property="og:image:secure_url">',
            'http://fork-cms.com/image.jpg?foo=bar' => '<meta content="http://fork-cms.com/image.jpg?foo=bar" property="og:image">',
            'https://fork-cms.com/image.jpg?foo=bar' => '<meta content="https://fork-cms.com/image.jpg?foo=bar" property="og:image">
<meta content="https://fork-cms.com/image.jpg?foo=bar" property="og:image:secure_url">',
        ];
        foreach ($imageUrls as $imageUrl => $expected) {
            $collection = new MetaCollection(false);
            $collection->addOpenGraphImage($imageUrl);
            self::assertSame($expected, (string) $collection);
        }
    }
}
