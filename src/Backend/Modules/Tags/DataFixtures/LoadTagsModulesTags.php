<?php

namespace Backend\Modules\Tags\DataFixtures;

use SpoonDatabase;

class LoadTagsModulesTags
{
    public function load(SpoonDatabase $database): void
    {
        $database->insert(
            'TagsModuleTag',
            [
                [
                    'moduleName' => 'Pages',
                    'tag_id' => LoadTagsTags::TAGS_TAG_1_ID,
                    'moduleId' => 1, // @TODO switch this to the constant in Fork 6
                ],
                [
                    'moduleName' => 'Pages',
                    'tag_id' => LoadTagsTags::TAGS_TAG_2_ID,
                    'moduleId' => 1, // @TODO switch this to the constant in Fork 6
                ],
                [
                    'moduleName' => 'Pages',
                    'tag_id' => LoadTagsTags::TAGS_TAG_2_ID,
                    'moduleId' => 2, // @TODO switch this to the constant in Fork 6
                ],
                [
                    'moduleName' => 'Pages',
                    'tag_id' => LoadTagsTags::TAGS_TAG_2_ID,
                    'moduleId' => 3, // @TODO switch this to the constant in Fork 6
                ],
                [
                    'moduleName' => 'Pages',
                    'tag_id' => LoadTagsTags::TAGS_TAG_2_ID,
                    'moduleId' => 404, // @TODO switch this to the constant in Fork 6
                ],
                [
                    'moduleName' => 'Pages',
                    'tag_id' => LoadTagsTags::TAGS_TAG_2_ID,
                    'moduleId' => 405,
                ],
                [
                    'moduleName' => 'Pages',
                    'tag_id' => LoadTagsTags::TAGS_TAG_2_ID,
                    'moduleId' => 406,
                ],
                [
                    'moduleName' => 'Faq',
                    'tag_id' => LoadTagsTags::TAGS_TAG_1_ID,
                    'moduleId' => 1, // @TODO switch this to the constant in Fork 6
                ],
            ]
        );
    }
}
