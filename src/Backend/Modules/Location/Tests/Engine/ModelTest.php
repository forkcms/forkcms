<?php

namespace Backend\Modules\Location\Tests\Engine;

use Backend\Modules\Location\Engine\Model;
use Common\WebTestCase;

class ModelTest extends WebTestCase
{
    public function setUp()
    {
        parent::setUp();

        if (!defined('APPLICATION')) {
            define('APPLICATION', 'Backend');
        }
    }

    public function testInsertingLocation(): void
    {
        $client = self::createClient();
        $this->loadFixtures($client);

        $locationArray = $this->getLocationArray();

        Model::insert($locationArray);

        $addedLocation = Model::get(1);

        self::assertEquals($locationArray['language'], $addedLocation['language']);
        self::assertEquals($locationArray['title'], $addedLocation['title']);
        self::assertEquals($locationArray['street'], $addedLocation['street']);
        self::assertEquals($locationArray['number'], $addedLocation['number']);
        self::assertEquals($locationArray['zip'], $addedLocation['zip']);
        self::assertEquals($locationArray['city'], $addedLocation['city']);
        self::assertEquals($locationArray['country'], $addedLocation['country']);
        self::assertEquals($locationArray['lat'], $addedLocation['lat']);
        self::assertEquals($locationArray['lng'], $addedLocation['lng']);
        self::assertEquals($locationArray['show_overview'], $addedLocation['show_overview']);
    }

    public function testInsertingLocationSetting(): void
    {
        Model::setMapSetting(
            1,
            'foo',
            'bar'
        );

        Model::setMapSetting(
            1,
            'ping',
            'pong'
        );

        self::assertEquals('bar', Model::getMapSetting(1, 'foo'));
        self::assertEquals('pong', Model::getMapSetting(1, 'ping'));
    }

    public function testEditingLocationSetting(): void
    {
        Model::setMapSetting(
            1,
            'foo',
            'pong'
        );

        Model::setMapSetting(
            1,
            'ping',
            'bar'
        );

        self::assertEquals('pong', Model::getMapSetting(1, 'foo'));
        self::assertEquals('bar', Model::getMapSetting(1, 'ping'));
    }

    public function testGettingAllLocationSettings(): void
    {
        $settings = Model::getMapSettings(1);

        self::assertCount(2, $settings);
        self::assertEquals('pong', $settings['foo']);
        self::assertEquals('bar', $settings['ping']);
    }

    public function testGettingNonexistantSettingReturnsFalse(): void
    {
        self::assertFalse(Model::getMapSetting(1, 'i-dont-exist'));
        self::assertFalse(Model::getMapSetting(3, 'the-location-also-doesnt-exist'));
    }

    public function testLocationExists(): void
    {
        self::assertEquals(true, Model::exists(1));
        self::assertEquals(false, Model::exists(2));
    }

    public function testGettingAllLocations(): void
    {
        $locations = Model::getAll();

        self::assertCount(1, $locations);

        $firstLocation = $locations[0];

        $locationArray = $this->getLocationArray();

        self::assertEquals($locationArray['language'], $firstLocation['language']);
        self::assertEquals($locationArray['title'], $firstLocation['title']);
        self::assertEquals($locationArray['street'], $firstLocation['street']);
        self::assertEquals($locationArray['number'], $firstLocation['number']);
        self::assertEquals($locationArray['zip'], $firstLocation['zip']);
        self::assertEquals($locationArray['city'], $firstLocation['city']);
        self::assertEquals($locationArray['country'], $firstLocation['country']);
        self::assertEquals($locationArray['lat'], $firstLocation['lat']);
        self::assertEquals($locationArray['lng'], $firstLocation['lng']);
        self::assertEquals($locationArray['show_overview'], $firstLocation['show_overview']);
    }

    public function testEditingLocation(): void
    {
        $locationArray = $this->getUpdatedLocationArray();

        Model::update($locationArray);

        $editedLocation = Model::get(1);

        self::assertEquals($locationArray['language'], $editedLocation['language']);
        self::assertEquals($locationArray['title'], $editedLocation['title']);
        self::assertEquals($locationArray['street'], $editedLocation['street']);
        self::assertEquals($locationArray['number'], $editedLocation['number']);
        self::assertEquals($locationArray['zip'], $editedLocation['zip']);
        self::assertEquals($locationArray['city'], $editedLocation['city']);
        self::assertEquals($locationArray['country'], $editedLocation['country']);
        self::assertEquals($locationArray['lat'], $editedLocation['lat']);
        self::assertEquals($locationArray['lng'], $editedLocation['lng']);
        self::assertEquals($locationArray['show_overview'], $editedLocation['show_overview']);
    }

    public function testDeletingLocation(): void
    {
        self::assertEquals(true, Model::exists(1));

        Model::delete(1);

        self::assertEquals(false, Model::exists(1));
    }

    private function getLocationArray(): array
    {
        return [
            'language' => 'en',
            'title' => 'Test location',
            'street' => 'Afrikalaan',
            'number' => '289',
            'zip' => '9000',
            'city' => 'Ghent',
            'country' => 'BE',
            'lat' => '51.0728',
            'lng' => '3.73599',
            'show_overview' => '1'
        ];
    }

    private function getUpdatedLocationArray(): array
    {
        return [
            'id' => '1',
            'extra_id' => '999',
            'language' => 'en',
            'title' => 'Test location edit',
            'street' => 'Visserij',
            'number' => '1',
            'zip' => '9000',
            'city' => 'Ghent',
            'country' => 'BE',
            'lat' => '51.0713',
            'lng' => '3.73523',
            'show_overview' => '0'
        ];
    }
}
