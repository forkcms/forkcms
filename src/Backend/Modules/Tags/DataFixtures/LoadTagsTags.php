<?php

namespace Backend\Modules\Tags\DataFixtures;

use SpoonDatabase;

class LoadTagsTags
{
    public function load(SpoonDatabase $database): void
    {
        $database->insert(
            'tags',
            [
                [
                    'id' => 1,
                    'language' => 'en',
                    'tag' => 'test',
                    'number' => 2,
                    'url' => 'test',
                ],
                [
                    'id' => 2,
                    'language' => 'en',
                    'tag' => 'most used',
                    'number' => 5,
                    'url' => 'most-used',
                ],
            ]
        );
    }
}
