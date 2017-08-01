<?php

namespace Backend\Modules\Analytics\Tests\GoogleClient;

use Backend\Modules\Analytics\GoogleClient\Connector;
use Common\ModulesSettings;
use Google_Client;
use Google_Service_Analytics;
use MatthiasMullie\Scrapbook\Adapters\MemoryStore;
use MatthiasMullie\Scrapbook\Psr6\Pool;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

class ConnectorTest extends TestCase
{
    public function testGetPageViews(): void
    {
        $connector = new Connector(
            $this->getAnalyticsServiceMock(),
            new Pool(new MemoryStore()),
            $this->getModulesSettingsMock()
        );

        self::assertEquals(
            1,
            $connector->getPageViews(
                strtotime('-1 day', mktime(0, 0, 0)),
                mktime(0, 0, 0)
            )
        );
    }

    public function testGetVisitors(): void
    {
        $connector = new Connector(
            $this->getAnalyticsServiceMock(),
            new Pool(new MemoryStore()),
            $this->getModulesSettingsMock()
        );

        self::assertEquals(
            2,
            $connector->getVisitors(
                strtotime('-1 day', mktime(0, 0, 0)),
                mktime(0, 0, 0)
            )
        );
    }

    public function testGetPagesPerVisit(): void
    {
        $connector = new Connector(
            $this->getAnalyticsServiceMock(),
            new Pool(new MemoryStore()),
            $this->getModulesSettingsMock()
        );

        self::assertEquals(
            3.14,
            $connector->getPagesPerVisit(
                strtotime('-1 day', mktime(0, 0, 0)),
                mktime(0, 0, 0)
            )
        );
    }

    public function testGetTimeOnSite(): void
    {
        $connector = new Connector(
            $this->getAnalyticsServiceMock(),
            new Pool(new MemoryStore()),
            $this->getModulesSettingsMock()
        );

        self::assertEquals(
            1.02,
            $connector->getTimeOnSite(
                strtotime('-1 day', mktime(0, 0, 0)),
                mktime(0, 0, 0)
            )
        );
    }

    public function testGetNewSessionsPercentage(): void
    {
        $connector = new Connector(
            $this->getAnalyticsServiceMock(),
            new Pool(new MemoryStore()),
            $this->getModulesSettingsMock()
        );

        self::assertEquals(
            78.23,
            $connector->getNewSessionsPercentage(
                strtotime('-1 day', mktime(0, 0, 0)),
                mktime(0, 0, 0)
            )
        );
    }

    public function testGetBounceRate(): void
    {
        $connector = new Connector(
            $this->getAnalyticsServiceMock(),
            new Pool(new MemoryStore()),
            $this->getModulesSettingsMock()
        );

        self::assertEquals(
            23.25,
            $connector->getBounceRate(
                strtotime('-1 day', mktime(0, 0, 0)),
                mktime(0, 0, 0)
            )
        );
    }

    public function testGetVisitorsGraphData(): void
    {
        ini_set('date.timezone', 'Europe/Brussels');

        $connector = new Connector(
            $this->getAnalyticsServiceMock(),
            new Pool(new MemoryStore()),
            $this->getModulesSettingsMock()
        );

        self::assertEquals(
            [
                [
                    'ga_date' => '1431295200',
                    'ga_pageviews' => '0',
                    'ga_users' => '0',
                ],
                [
                    'ga_date' => '1431381600',
                    'ga_pageviews' => '1',
                    'ga_users' => '1',
                ],
            ],
            $connector->getVisitorsGraphData(
                strtotime('-1 day', mktime(0, 0, 0)),
                mktime(0, 0, 0)
            )
        );
    }

    public function testGetSourceGraphData(): void
    {
        $connector = new Connector(
            $this->getAnalyticsServiceMock(),
            new Pool(new MemoryStore()),
            $this->getModulesSettingsMock()
        );

        self::assertEquals(
            [
                [
                    'ga_medium' => '(none)',
                    'ga_pageviews' => '8',
                ],
                [
                    'ga_medium' => 'organic',
                    'ga_pageviews' => '6',
                ],
            ],
            $connector->getSourceGraphData(
                strtotime('-1 day', mktime(0, 0, 0)),
                mktime(0, 0, 0)
            )
        );
    }

    public function testGetMostVisitedPagesData(): void
    {
        $connector = new Connector(
            $this->getAnalyticsServiceMock(),
            new Pool(new MemoryStore()),
            $this->getModulesSettingsMock()
        );

        self::assertEquals(
            [
                [
                    'ga_pagePath' => '/en',
                    'ga_pageviews' => '15',
                ],
                [
                    'ga_pagePath' => '/en/blog',
                    'ga_pageviews' => '8',
                ],
            ],
            $connector->getMostVisitedPagesData(
                strtotime('-1 day', mktime(0, 0, 0)),
                mktime(0, 0, 0)
            )
        );
    }

    private function getModulesSettingsMock(): PHPUnit_Framework_MockObject_MockObject
    {
        return $this->getMockBuilder(ModulesSettings::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }

    private function getAnalyticsServiceMock(): Google_Service_Analytics
    {
        $analyticsService = new Google_Service_Analytics(new Google_Client());

        $dataGateway = $this->getMockBuilder('Google_Service_Analytics_DataGa_Resource')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $metricsReturnMock = [
            'totalsForAllResults' => [
                'ga:pageviews' => 1,
                'ga:users' => 2,
                'ga:pageviewsPerSession' => 3.14,
                'ga:avgSessionDuration' => 1.02,
                'ga:percentNewSessions' => 78.23,
                'ga:bounceRate' => 23.25,
            ],
        ];

        $visitGraphDataMock = [
            'rows' => [
                ['20150511', '0', '0'],
                ['20150512', '1', '1'],
            ],
            'columnHeaders' => [
                ['name' => 'ga:date'],
                ['name' => 'ga:pageviews'],
                ['name' => 'ga:users'],
            ],
        ];

        $sourceGraphDataMock = [
            'rows' => [
                ['(none)', '8'],
                ['organic', '6'],
            ],
            'columnHeaders' => [
                ['name' => 'ga:medium'],
                ['name' => 'ga:pageviews'],
            ],
        ];

        $pageViewsDataMock = [
            'rows' => [
                ['/en', '15'],
                ['/en/blog', '8'],
            ],
            'columnHeaders' => [
                ['name' => 'ga:pagePath'],
                ['name' => 'ga:pageviews'],
            ],
        ];

        $dataGateway->method('get')
            ->will(self::onConsecutiveCalls(
                $metricsReturnMock,
                $visitGraphDataMock,
                $pageViewsDataMock,
                $sourceGraphDataMock
            ))
        ;

        $analyticsService->data_ga = $dataGateway;

        return $analyticsService;
    }
}
