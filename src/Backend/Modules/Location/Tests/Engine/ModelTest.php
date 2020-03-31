<?php

namespace Backend\Modules\Location\Tests\Engine;

use Backend\Modules\Location\DataFixtures\LoadLocation;
use Backend\Modules\Location\DataFixtures\LoadLocationSettings;
use Backend\Modules\Location\Engine\Model;
use Backend\Core\Tests\BackendWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class ModelTest extends BackendWebTestCase
{
    public function testInsertingLocation(): void
    {
        $locationArray = LoadLocation::LOCATION_LOCATION_DATA;

        $id = Model::insert($locationArray);

        $addedLocation = Model::get($id);

        self::assertEquals($locationArray['locale'], $addedLocation['language']);
        self::assertEquals($locationArray['title'], $addedLocation['title']);
        self::assertEquals($locationArray['street'], $addedLocation['street']);
        self::assertEquals($locationArray['number'], $addedLocation['number']);
        self::assertEquals($locationArray['zip'], $addedLocation['zip']);
        self::assertEquals($locationArray['city'], $addedLocation['city']);
        self::assertEquals($locationArray['country'], $addedLocation['country']);
        self::assertEquals($locationArray['latitude'], $addedLocation['lat']);
        self::assertEquals($locationArray['longitude'], $addedLocation['lng']);
        self::assertEquals($locationArray['showInOverview'], $addedLocation['show_overview']);
    }

    public function testInsertingLocationSetting(Client $client): void
    {
        $this->loadFixtures(
            $client,
            [
                LoadLocation::class,
            ]
        );

        $locationId = LoadLocation::getLocationId();

        self::assertFalse(Model::getMapSetting($locationId, LoadLocationSettings::SETTING_NAME_1));
        self::assertFalse(Model::getMapSetting($locationId, LoadLocationSettings::SETTING_NAME_2));

        Model::setMapSetting(
            $locationId,
            LoadLocationSettings::SETTING_NAME_1,
            LoadLocationSettings::SETTING_VALUE_1
        );

        Model::setMapSetting(
            $locationId,
            LoadLocationSettings::SETTING_NAME_2,
            LoadLocationSettings::SETTING_VALUE_2
        );

        self::assertEquals(
            LoadLocationSettings::SETTING_VALUE_1,
            Model::getMapSetting(
                $locationId,
                LoadLocationSettings::SETTING_NAME_1
            )
        );
        self::assertEquals(
            LoadLocationSettings::SETTING_VALUE_2,
            Model::getMapSetting(
                $locationId,
                LoadLocationSettings::SETTING_NAME_2
            )
        );
    }

    public function testEditingLocationSetting(Client $client): void
    {
        $this->loadFixtures(
            $client,
            [
                LoadLocation::class,
                LoadLocationSettings::class,
            ]
        );

        $locationId = LoadLocation::getLocationId();
        $newValue1 = 'fork';
        $newValue2 = 'cms';

        self::assertNotEquals($newValue1, Model::getMapSetting($locationId, LoadLocationSettings::SETTING_NAME_1));
        self::assertNotEquals($newValue2, Model::getMapSetting($locationId, LoadLocationSettings::SETTING_NAME_2));

        Model::setMapSetting(
            $locationId,
            LoadLocationSettings::SETTING_NAME_1,
            $newValue1
        );

        Model::setMapSetting(
            $locationId,
            LoadLocationSettings::SETTING_NAME_2,
            $newValue2
        );

        self::assertEquals($newValue1, Model::getMapSetting($locationId, LoadLocationSettings::SETTING_NAME_1));
        self::assertEquals($newValue2, Model::getMapSetting($locationId, LoadLocationSettings::SETTING_NAME_2));
    }

    public function testGettingAllLocationSettings(Client $client): void
    {
        $this->loadFixtures(
            $client,
            [
                LoadLocation::class,
                LoadLocationSettings::class,
            ]
        );

        $settings = Model::getMapSettings(LoadLocation::getLocationId());

        self::assertCount(2, $settings);
        self::assertEquals(LoadLocationSettings::SETTING_VALUE_1, $settings[LoadLocationSettings::SETTING_NAME_1]);
        self::assertEquals(LoadLocationSettings::SETTING_VALUE_2, $settings[LoadLocationSettings::SETTING_NAME_2]);
    }

    public function testGettingNonExistentSettingReturnsFalse(Client $client): void
    {
        $this->loadFixtures(
            $client,
            [
                LoadLocation::class,
                LoadLocationSettings::class,
            ]
        );

        self::assertFalse(Model::getMapSetting(LoadLocation::getLocationId(), 'i-dont-exist'));
        self::assertFalse(Model::getMapSetting(9000, 'the-location-also-doesnt-exist'));
    }

    public function testLocationExists(Client $client): void
    {
        $this->loadFixtures(
            $client,
            [
                LoadLocation::class,
            ]
        );

        self::assertTrue(Model::exists(LoadLocation::getLocationId()));
        self::assertFalse(Model::exists(2));
    }

    public function testGettingAllLocations(Client $client): void
    {
        $this->loadFixtures(
            $client,
            [
                LoadLocation::class,
            ]
        );

        $locations = Model::getAll();

        self::assertCount(1, $locations);

        $firstLocation = $locations[0];

        $locationArray = LoadLocation::LOCATION_LOCATION_DATA;

        self::assertEquals($locationArray['locale'], $firstLocation['language']);
        self::assertEquals($locationArray['title'], $firstLocation['title']);
        self::assertEquals($locationArray['street'], $firstLocation['street']);
        self::assertEquals($locationArray['number'], $firstLocation['number']);
        self::assertEquals($locationArray['zip'], $firstLocation['zip']);
        self::assertEquals($locationArray['city'], $firstLocation['city']);
        self::assertEquals($locationArray['country'], $firstLocation['country']);
        self::assertEquals($locationArray['latitude'], $firstLocation['lat']);
        self::assertEquals($locationArray['longitude'], $firstLocation['lng']);
        self::assertEquals($locationArray['showInOverview'], $firstLocation['show_overview']);
    }

    public function testEditingLocation(Client $client): void
    {
        $this->loadFixtures(
            $client,
            [
                LoadLocation::class,
            ]
        );

        $locationArray = [
            'id' => LoadLocation::getLocationId(),
            'extra_id' => LoadLocation::getExtraId(),
            'language' => 'en',
            'title' => 'Test location edit',
            'street' => 'Visserij',
            'number' => '1',
            'zip' => '9000',
            'city' => 'Ghent',
            'country' => 'BE',
            'lat' => '51.0713',
            'lng' => '3.73523',
            'show_overview' => '0',
        ];

        $location = Model::get($locationArray['id']);

        self::assertEquals($locationArray['language'], $location['language']);
        self::assertNotEquals($locationArray['title'], $location['title']);
        self::assertNotEquals($locationArray['street'], $location['street']);
        self::assertNotEquals($locationArray['number'], $location['number']);
        self::assertEquals($locationArray['zip'], $location['zip']);
        self::assertEquals($locationArray['city'], $location['city']);
        self::assertEquals($locationArray['country'], $location['country']);
        self::assertNotEquals($locationArray['lat'], $location['lat']);
        self::assertNotEquals($locationArray['lng'], $location['lng']);
        self::assertNotEquals($locationArray['show_overview'], $location['show_overview']);

        Model::update($locationArray);

        $editedLocation = Model::get($locationArray['id']);

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

    public function testDeletingLocation(Client $client): void
    {
        $this->loadFixtures(
            $client,
            [
                LoadLocation::class,
                LoadLocationSettings::class,
            ]
        );

        $locationId = LoadLocation::getLocationId();

        self::assertTrue(Model::exists($locationId));
        self::assertNotEmpty(Model::getMapSettings($locationId));

        Model::delete($locationId);

        self::assertFalse(Model::exists($locationId));
        self::assertEmpty(Model::getMapSettings($locationId));
    }
}
