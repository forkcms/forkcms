<?php

namespace Backend\Modules\Analytics\Tests\GoogleClient;

use Backend\Modules\Analytics\GoogleClient\Connector;
use Common\ModulesSettings;
use Google_Client;
use Google_Service_Analytics;
use MatthiasMullie\Scrapbook\Adapters\MemoryStore;
use MatthiasMullie\Scrapbook\Psr6\Pool;
use PHPUnit\Framework\TestCase;

class ConnectorTest extends TestCase
{
    public function testGetPageViews()
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

    public function testGetVisitors()
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

    public function testGetPagesPerVisit()
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

    public function testGetTimeOnSite()
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

    public function testGetNewSessionsPercentage()
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

    public function testGetBounceRate()
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

    public function testGetVisitorsGraphData()
    {
        ini_set('date.timezone', 'Europe/Brussels');

        $connector = new Connector(
            $this->getAnalyticsServiceMock(),
            new Pool(new MemoryStore()),
            $this->getModulesSettingsMock()
        );

        self::assertEquals(
            array(
                array(
                    'ga_date' => '1431295200',
                    'ga_pageviews' => '0',
                    'ga_users' => '0',
                ),
                array(
                    'ga_date' => '1431381600',
                    'ga_pageviews' => '1',
                    'ga_users' => '1',
                ),
            ),
            $connector->getVisitorsGraphData(
                strtotime('-1 day', mktime(0, 0, 0)),
                mktime(0, 0, 0)
            )
        );
    }

    public function testGetSourceGraphData()
    {
        $connector = new Connector(
            $this->getAnalyticsServiceMock(),
            new Pool(new MemoryStore()),
            $this->getModulesSettingsMock()
        );

        self::assertEquals(
            array(
                array(
                    'ga_medium' => '(none)',
                    'ga_pageviews' => '8',
                ),
                array(
                    'ga_medium' => 'organic',
                    'ga_pageviews' => '6',
                ),
            ),
            $connector->getSourceGraphData(
                strtotime('-1 day', mktime(0, 0, 0)),
                mktime(0, 0, 0)
            )
        );
    }

    public function testGetMostVisitedPagesData()
    {
        $connector = new Connector(
            $this->getAnalyticsServiceMock(),
            new Pool(new MemoryStore()),
            $this->getModulesSettingsMock()
        );

        self::assertEquals(
            array(
                array(
                    'ga_pagePath' => '/en',
                    'ga_pageviews' => '15',
                ),
                array(
                    'ga_pagePath' => '/en/blog',
                    'ga_pageviews' => '8',
                ),
            ),
            $connector->getMostVisitedPagesData(
                strtotime('-1 day', mktime(0, 0, 0)),
                mktime(0, 0, 0)
            )
        );
    }

    private function getModulesSettingsMock()
    {
        return $this->getMockBuilder(ModulesSettings::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }

    private function getAnalyticsServiceMock()
    {
        $analyticsService = new Google_Service_Analytics(new Google_Client());

        $dataGateway = $this->getMockBuilder('Google_Service_Analytics_DataGa_Resource')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $metricsReturnMock = array(
            'totalsForAllResults' => array(
                'ga:pageviews' => 1,
                'ga:users' => 2,
                'ga:pageviewsPerSession' => 3.14,
                'ga:avgSessionDuration' => 1.02,
                'ga:percentNewSessions' => 78.23,
                'ga:bounceRate' => 23.25,
            ),
        );

        $visitGraphDataMock = array(
            'rows' => array(
                array('20150511', '0', '0'),
                array('20150512', '1', '1'),
            ),
            'columnHeaders' => array(
                array('name' => 'ga:date'),
                array('name' => 'ga:pageviews'),
                array('name' => 'ga:users'),
            ),
        );

        $sourceGraphDataMock = array(
            'rows' => array(
                array('(none)', '8'),
                array('organic', '6'),
            ),
            'columnHeaders' => array(
                array('name' => 'ga:medium'),
                array('name' => 'ga:pageviews'),
            ),
        );

        $pageViewsDataMock = array(
            'rows' => array(
                array('/en', '15'),
                array('/en/blog', '8'),
            ),
            'columnHeaders' => array(
                array('name' => 'ga:pagePath'),
                array('name' => 'ga:pageviews'),
            ),
        );

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
