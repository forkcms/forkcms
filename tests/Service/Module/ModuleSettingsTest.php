<?php

namespace App\Tests\Service\Module;

use App\Service\Module\ModuleSettings;
use MatthiasMullie\Scrapbook\Adapters\MemoryStore;
use MatthiasMullie\Scrapbook\Psr6\Pool;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Tests for our module settings
 */
class ModuleSettingsTest extends TestCase
{
    public function testFetchingSettingsCallsTheDatabaseOnce(): void
    {
        $moduleSettings = new ModuleSettings(
            $this->getDatabaseMock(),
            new Pool(new MemoryStore())
        );

        $moduleSettings->get('Core', 'theme', 'Fork');
        $moduleSettings->get('Core', 'time_format', 'H:i');
        $moduleSettings->get('Blog', 'spam_filter', false);
    }

    public function testFetchingSettingWorks(): void
    {
        $moduleSettings = new ModuleSettings(
            $this->getDatabaseMock(),
            new Pool(new MemoryStore())
        );

        self::assertEquals(
            'Fork',
            $moduleSettings->get('Core', 'theme')
        );
        self::assertEquals(
            'Fork',
            $moduleSettings->get('Core', 'theme', 'test')
        );
    }

    public function testDefaultValueWillBeReturned(): void
    {
        $moduleSettings = new ModuleSettings(
            $this->getDatabaseMock(),
            new Pool(new MemoryStore())
        );

        self::assertEquals(
            'default_value',
            $moduleSettings->get('Test', 'Blub', 'default_value')
        );
    }

    public function testFetchingSettingsForAModule(): void
    {
        $moduleSettings = new ModuleSettings(
            $this->getDatabaseMock(),
            new Pool(new MemoryStore())
        );

        self::assertEquals(
            [
                'theme' => 'Fork',
            ],
            $moduleSettings->getForModule('Core')
        );
        self::assertEquals(
            [],
            $moduleSettings->getForModule('Fake')
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
