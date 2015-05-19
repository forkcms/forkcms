<?php

namespace Common\Tests;

use Common\ModulesSettings;
use Common\Cache\InMemoryCache;
use PHPUnit_Framework_TestCase;

/**
 * Tests for our module settings
 *
 * @author Wouter Sioen <wouter@sumocoders.be>
 */
class ModulesSettingsTest extends PHPUnit_Framework_TestCase
{
    public function testFetchingSettingsCallsTheDatabaseOnce()
    {
        $modulesSettings = new ModulesSettings(
            $this->getDatabaseMock(),
            new InMemoryCache()
        );

        $modulesSettings->get('Core', 'theme', 'triton');
        $modulesSettings->get('Core', 'time_format', 'H:i');
        $modulesSettings->get('Blog', 'spam_filter', false);
    }

    public function testFetchingSettingWorks()
    {
        $modulesSettings = new ModulesSettings(
            $this->getDatabaseMock(),
            new InMemoryCache()
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
            new InMemoryCache()
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
            new InMemoryCache()
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

    public function testSettingAValueSavesIt()
    {
        $databaseMock = $this->getDatabaseMock();

        $databaseMock
            ->expects($this->once())
            ->method('execute')
        ;

        $modulesSettings = new ModulesSettings(
            $databaseMock,
            new InMemoryCache()
        );

        $this->assertEquals(
            'triton',
            $modulesSettings->get('Core', 'theme')
        );
        $modulesSettings->set('Core', 'theme', 'test_theme');
        $this->assertEquals(
            'test_theme',
            $modulesSettings->get('Core', 'theme')
        );
    }

    public function testSettingAValueForANewModule()
    {
        $databaseMock = $this->getDatabaseMock();

        $databaseMock
            ->expects($this->once())
            ->method('execute')
        ;

        $modulesSettings = new ModulesSettings(
            $databaseMock,
            new InMemoryCache()
        );

        $this->assertNull(
            $modulesSettings->get('Fake', 'module')
        );
        $modulesSettings->set('Fake', 'module', 'value');
        $this->assertEquals(
            'value',
            $modulesSettings->get('Fake', 'module')
        );
    }

    public function testDeletingAValueDeletesIt()
    {
        $databaseMock = $this->getDatabaseMock();

        $databaseMock
            ->expects($this->once())
            ->method('delete')
        ;

        $modulesSettings = new ModulesSettings(
            $databaseMock,
            new InMemoryCache()
        );
        $this->assertEquals(
            'triton',
            $modulesSettings->get('Core', 'theme')
        );
        $modulesSettings->delete('Core', 'theme');
        $this->assertNull($modulesSettings->get('Core', 'theme'));
        $this->assertEquals(
            'default_value',
            $modulesSettings->get('Core', 'theme', 'default_value')
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
                )
            ))
        ;

        return $databaseMock;
    }
}
