<?php

namespace Backend\Modules\Tags\DataFixtures;

use SpoonDatabase;

class LoadTagsModulesTags
{
    public function load(SpoonDatabase $database): void
    {
        $database->insert(
            'modules_tags',
            [
                [
                    'module' => 'Pages',
                    'tag_id' => LoadTagsTags::TAGS_TAG_1_ID,
                    'other_id' => 1, // @TODO switch this to the constant in Fork 6
                ],
                [
                    'module' => 'Pages',
                    'tag_id' => LoadTagsTags::TAGS_TAG_2_ID,
                    'other_id' => 1, // @TODO switch this to the constant in Fork 6
                ],
                [
                    'module' => 'Pages',
                    'tag_id' => LoadTagsTags::TAGS_TAG_2_ID,
                    'other_id' => 2, // @TODO switch this to the constant in Fork 6
                ],
                [
                    'module' => 'Pages',
                    'tag_id' => LoadTagsTags::TAGS_TAG_2_ID,
                    'other_id' => 3, // @TODO switch this to the constant in Fork 6
                ],
                [
                    'module' => 'Pages',
                    'tag_id' => LoadTagsTags::TAGS_TAG_2_ID,
                    'other_id' => 404, // @TODO switch this to the constant in Fork 6
                ],
                [
                    'module' => 'Pages',
                    'tag_id' => LoadTagsTags::TAGS_TAG_2_ID,
                    'other_id' => 405,
                ],
                [
                    'module' => 'Pages',
                    'tag_id' => LoadTagsTags::TAGS_TAG_2_ID,
                    'other_id' => 406,
                ],
                [
                    'module' => 'Faq',
                    'tag_id' => LoadTagsTags::TAGS_TAG_1_ID,
                    'other_id' => 1, // @TODO switch this to the constant in Fork 6
                ],
            ]
        );
    }
}
