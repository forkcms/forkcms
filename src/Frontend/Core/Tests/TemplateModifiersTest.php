<?php

namespace Frontend\Core\Tests;

use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\TemplateModifiers;
use PHPUnit_Framework_TestCase;

class TemplateModifiersTest extends PHPUnit_Framework_TestCase
{
    public function test_format_currency()
    {
        $this->assertEquals(
            '€&nbsp;1,23',
            TemplateModifiers::formatCurrency(1.2324, 'EUR', 2)
        );

        $this->assertEquals(
            '€&nbsp;1,23',
            TemplateModifiers::formatCurrency(1.2324, 'EUR', null)
        );

        $this->assertEquals(
            '€&nbsp;1',
            TemplateModifiers::formatCurrency(1.2324, 'EUR', 0)
        );

        $this->assertEquals(
            '€&nbsp;1,2324',
            TemplateModifiers::formatCurrency(1.2324, 'EUR', 4)
        );

        $this->assertEquals(
            'USD&nbsp;1,23',
            TemplateModifiers::formatCurrency(1.2324, 'USD')
        );
    }

    public function test_format_float()
    {
        $this->assertEquals(
            '1.2344',
            TemplateModifiers::formatFloat(1.2344, 4)
        );

        $this->assertEquals(
            '1.234',
            TemplateModifiers::formatFloat(1.2344, 3)
        );

        $this->assertEquals(
            '1',
            TemplateModifiers::formatFloat(1.2344, 0)
        );
    }

    public function test_stripnewlines()
    {
        $this->assertEquals(
            'Foo Bar',
            TemplateModifiers::stripNewlines("Foo\nBar")
        );

        $this->assertEquals(
            'Foo Bar',
            TemplateModifiers::stripNewlines("Foo\rBar")
        );

        $this->assertEquals(
            'Foo Bar',
            TemplateModifiers::stripNewlines("Foo\r\nBar")
        );
    }

    public function test_truncate()
    {
        $containerMock = $this
            ->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $containerMock->expects($this->any())
            ->method('getParameter')
            ->with('kernel.charset')
            ->will($this->returnValue('UTF-8'))
        ;

        FrontendModel::setContainer($containerMock);

        $this->assertEquals(
            TemplateModifiers::truncate('foo bar baz qux', 3, false, true),
            'foo'
        );

        $this->assertEquals(
            TemplateModifiers::truncate('foo bar baz qux', 4, false, true),
            'foo'
        );

        $this->assertEquals(
            TemplateModifiers::truncate('foo bar baz qux', 8, false, true),
            'foo bar'
        );

        $this->assertEquals(
            TemplateModifiers::truncate('foo bar baz qux', 100, false, true),
            'foo bar baz qux'
        );

        // Hellip
        $this->assertEquals(
            TemplateModifiers::truncate('foo bar baz qux', 5, true, true),
            'foo…'
        );

        $this->assertEquals(
            TemplateModifiers::truncate('foo bar baz qux', 14, true, true),
            'foo bar baz…'
        );

        $this->assertEquals(
            TemplateModifiers::truncate('foo bar baz qux', 15, true, true),
            'foo bar baz qux'
        );
    }
}
