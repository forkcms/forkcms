<?php

namespace Common\Doctrine\Entity\Meta;

use Common\Doctrine\Entity\Meta;
use Common\Doctrine\ValueObject\SEOFollow;
use Common\Doctrine\ValueObject\SEOIndex;
use PHPUnit\Framework\TestCase;

/**
 * Tests for our Meta entity
 */
class MetaTest extends TestCase
{
    public function testCloning()
    {
        $meta = new Meta(
            'fork, cms',
            true,
            'Fork CMS rules this world.',
            true,
            'Fork CMS rules this world.',
            true,
            'fork-cms-rules-this-world',
            true,
            '',
            new SEOFollow('none'),
            new SEOIndex('none'),
            [],
            1
        );

        $clonedMeta = clone $meta;
        $this->assertEquals(null, $clonedMeta->getId());
        $this->assertNotEquals($clonedMeta->getUrl(), $meta->getUrl());
        $this->assertEquals('fork-cms-rules-this-world-2', $clonedMeta->getUrl());
    }
}
