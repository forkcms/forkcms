<?php

namespace Backend\Modules\Tags\DataFixtures;

use SpoonDatabase;

class LoadTagsTags
{
    public function load(SpoonDatabase $database): void
    {
        $database->insert(
            'TagsTag',
            [
                [
                    'id' => 1,
                    'locale' => 'en',
                    'tag' => 'test',
                    'numberOfTimesLinked' => 2,
                    'url' => 'test',
                ],
                [
                    'id' => 2,
                    'locale' => 'en',
                    'tag' => 'most used',
                    'number' => 6,
                    'url' => 'most-used',
                ],
            ]
        );
    }
}
