<?php

namespace Frontend\Core\Tests;

use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\TemplateModifiers;
use PHPUnit\Framework\TestCase;

class TemplateModifiersTest extends TestCase
{
    public function testFormatFloat(): void
    {
        self::assertEquals(
            '1.2344',
            TemplateModifiers::formatFloat(1.2344, 4)
        );

        self::assertEquals(
            '1.234',
            TemplateModifiers::formatFloat(1.2344, 3)
        );

        self::assertEquals(
            '1',
            TemplateModifiers::formatFloat(1.2344, 0)
        );
    }

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
