<?php

namespace Backend\Core\Tests\Engine;

use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\TemplateModifiers;
use PHPUnit_Framework_TestCase;

class TemplateModifiersTest extends PHPUnit_Framework_TestCase
{
    public function test_stripnewlines()
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

    public function test_truncate()
    {
        $containerMock = $this
            ->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $containerMock->expects(self::any())
            ->method('getParameter')
            ->with('kernel.charset')
            ->will(self::returnValue('UTF-8'))
        ;

        BackendModel::setContainer($containerMock);

        self::assertEquals(
            TemplateModifiers::truncate('foo bar baz qux', 3, false, true),
            'foo'
        );

        self::assertEquals(
            TemplateModifiers::truncate('foo bar baz qux', 4, false, true),
            'foo'
        );

        self::assertEquals(
            TemplateModifiers::truncate('foo bar baz qux', 8, false, true),
            'foo bar'
        );

        self::assertEquals(
            TemplateModifiers::truncate('foo bar baz qux', 100, false, true),
            'foo bar baz qux'
        );

        // Hellip
        self::assertEquals(
            TemplateModifiers::truncate('foo bar baz qux', 5, true, true),
            'foo…'
        );

        self::assertEquals(
            TemplateModifiers::truncate('foo bar baz qux', 14, true, true),
            'foo bar baz…'
        );

        self::assertEquals(
            TemplateModifiers::truncate('foo bar baz qux', 15, true, true),
            'foo bar baz qux'
        );
    }
}
