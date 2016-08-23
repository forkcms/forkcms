<?php

namespace Common\Tests;

use Common\ModulesSettings;
use MatthiasMullie\Scrapbook\Adapters\MemoryStore;
use MatthiasMullie\Scrapbook\Psr6\Pool;
use PHPUnit_Framework_TestCase;

/**
 * Tests for our module settings
 */
class ModulesSettingsTest extends PHPUnit_Framework_TestCase
{
    public function testFetchingSettingsCallsTheDatabaseOnce()
    {
        $modulesSettings = new ModulesSettings(
            $this->getDatabaseMock(),
            new Pool(new MemoryStore())
        );

        $modulesSettings->get('Core', 'theme', 'triton');
        $modulesSettings->get('Core', 'time_format', 'H:i');
        $modulesSettings->get('Blog', 'spam_filter', false);
    }

    public function testFetchingSettingWorks()
    {
        $modulesSettings = new ModulesSettings(
            $this->getDatabaseMock(),
            new Pool(new MemoryStore())
        );

        $this->assertEquals(
            'triton',
            $modulesSettings->get('Core', 'theme')
        );
        $this->assertEquals(
            'triton',
            $modulesSettings->get('Core', 'theme', 'test')
        );
    }

    public function testDefaultValueWillBeReturned()
    {
        $modulesSettings = new ModulesSettings(
            $this->getDatabaseMock(),
            new Pool(new MemoryStore())
        );

        $this->assertEquals(
            'default_value',
            $modulesSettings->get('Test', 'Blub', 'default_value')
        );
    }

    public function testFetchingSettingsForAModule()
    {
        $modulesSettings = new ModulesSettings(
            $this->getDatabaseMock(),
            new Pool(new MemoryStore())
        );

        $this->assertEquals(
            array(
                'theme' => 'triton',
            ),
            $modulesSettings->getForModule('Core')
        );
        $this->assertEquals(
            array(),
            $modulesSettings->getForModule('Fake')
        );
    }

    private function getDatabaseMock()
    {
        $databaseMock = $this->getMockBuilder('SpoonDatabase')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $databaseMock
            ->expects($this->once())
            ->method('getRecords')
            ->willReturn(array(
                array(
                    'module' => 'Core',
                    'name' => 'theme',
                    'value' => serialize('triton'),
                ),
            ))
        ;

        return $databaseMock;
    }
}
