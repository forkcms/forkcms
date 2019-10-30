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
                    'tag_id' => 1,
                    'moduleId' => 1,
                ],
                [
                    'moduleName' => 'Pages',
                    'tag_id' => 2,
                    'moduleId' => 1,
                ],
                [
                    'moduleName' => 'Pages',
                    'tag_id' => 2,
                    'moduleId' => 2,
                ],
                [
                    'moduleName' => 'Pages',
                    'tag_id' => 2,
                    'moduleId' => 3,
                ],
                [
                    'moduleName' => 'Pages',
                    'tag_id' => 2,
                    'moduleId' => 404,
                ],
                [
                    'moduleName' => 'Pages',
                    'tag_id' => 2,
                    'moduleId' => 405,
                ],
                [
                    'moduleName' => 'Pages',
                    'tag_id' => 2,
                    'moduleId' => 406,
                ],
                [
                    'moduleName' => 'Faq',
                    'tag_id' => 1,
                    'moduleId' => 1,
                ],
            ]
        );
    }
}
