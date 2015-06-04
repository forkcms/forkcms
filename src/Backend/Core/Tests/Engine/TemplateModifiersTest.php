<?php

namespace Backend\Core\Tests\Engine;

use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\TemplateModifiers;
use PHPUnit_Framework_TestCase;

class TemplateModifiersTest extends PHPUnit_Framework_TestCase
{
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

        BackendModel::setContainer($containerMock);

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
