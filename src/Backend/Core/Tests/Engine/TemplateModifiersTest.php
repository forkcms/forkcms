<?php

namespace Backend\Core\Tests\Engine;

use Backend\Core\Engine\TemplateModifiers;
use PHPUnit\Framework\TestCase;

class TemplateModifiersTest extends TestCase
{
    public function testStripNewlines(): void
    {
        self::assertEquals(
            'Foo Bar',
            TemplateModifiers::stripNewlines("Foo\nBar")
        );

        self::assertEquals(
            'Foo Bar',
            TemplateModifiers::stripNewlines("Foo\rBar")
        );

        self::assertEquals(
            'Foo Bar',
            TemplateModifiers::stripNewlines("Foo\r\nBar")
        );
    }
}
