<?php

namespace Common\Tests;

use Common\ModulesSettings;
use MatthiasMullie\Scrapbook\Adapters\MemoryStore;
use MatthiasMullie\Scrapbook\Psr6\Pool;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Tests for our module settings
 */
class ModulesSettingsTest extends TestCase
{
    public function testFetchingSettingsCallsTheDatabaseOnce(): void
    {
        $modulesSettings = new ModulesSettings(
            $this->getDatabaseMock(),
            new Pool(new MemoryStore())
        );

        $modulesSettings->get('Core', 'theme', 'Fork');
        $modulesSettings->get('Core', 'time_format', 'H:i');
        $modulesSettings->get('Blog', 'spam_filter', false);
    }

    public function testFetchingSettingWorks(): void
    {
        $modulesSettings = new ModulesSettings(
            $this->getDatabaseMock(),
            new Pool(new MemoryStore())
        );

        self::assertEquals(
            'Fork',
            $modulesSettings->get('Core', 'theme')
        );
        self::assertEquals(
            'Fork',
            $modulesSettings->get('Core', 'theme', 'test')
        );
    }

    public function testDefaultValueWillBeReturned(): void
    {
        $modulesSettings = new ModulesSettings(
            $this->getDatabaseMock(),
            new Pool(new MemoryStore())
        );

        self::assertEquals(
            'default_value',
            $modulesSettings->get('Test', 'Blub', 'default_value')
        );
    }

    public function testFetchingSettingsForAModule(): void
    {
        $modulesSettings = new ModulesSettings(
            $this->getDatabaseMock(),
            new Pool(new MemoryStore())
        );

        self::assertEquals(
            [
                'theme' => 'Fork',
            ],
            $modulesSettings->getForModule('Core')
        );
        self::assertEquals(
            [],
            $modulesSettings->getForModule('Fake')
        );
    }

    private function getDatabaseMock(): PHPUnit_Framework_MockObject_MockObject
    {
        $databaseMock = $this->getMockBuilder('SpoonDatabase')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $databaseMock
            ->expects(self::atLeastOnce())
            ->method('getRecords')
            ->willReturn([
                [
                    'module' => 'Core',
                    'name' => 'theme',
                    'value' => serialize('Fork'),
                ],
            ])
        ;

        return $databaseMock;
    }
}
