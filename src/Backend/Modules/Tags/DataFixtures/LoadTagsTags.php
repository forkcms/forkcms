<?php

namespace Backend\Modules\Tags\DataFixtures;

use SpoonDatabase;

class LoadTagsTags
{
    public const TAGS_TAG_1_NAME = 'test';
    public const TAGS_TAG_1_ID = 1;
    public const TAGS_TAG_1_SLUG = 'test';
    public const TAGS_TAG_2_NAME = 'most used';
    public const TAGS_TAG_2_ID = 2;
    public const TAGS_TAG_2_SLUG = 'most-used';

    public function load(SpoonDatabase $database): void
    {
        $database->insert(
            'tags',
            [
                [
                    'id' => self::TAGS_TAG_1_ID,
                    'language' => 'en',
                    'tag' => self::TAGS_TAG_1_NAME,
                    'number' => 2,
                    'url' => self::TAGS_TAG_1_SLUG,
                ],
                [
                    'id' => self::TAGS_TAG_2_ID,
                    'language' => 'en',
                    'tag' => self::TAGS_TAG_2_NAME,
                    'number' => 6,
                    'url' => self::TAGS_TAG_2_SLUG,
                ],
            ]
        );
    }
}
