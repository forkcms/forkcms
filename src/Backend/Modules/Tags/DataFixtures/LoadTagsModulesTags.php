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
                    'tag_id' => 1,
                    'other_id' => 1,
                ],
                [
                    'module' => 'Pages',
                    'tag_id' => 2,
                    'other_id' => 1,
                ],
                [
                    'module' => 'Pages',
                    'tag_id' => 2,
                    'other_id' => 2,
                ],
                [
                    'module' => 'Pages',
                    'tag_id' => 2,
                    'other_id' => 3,
                ],
                [
                    'module' => 'Pages',
                    'tag_id' => 2,
                    'other_id' => 404,
                ],
                [
                    'module' => 'Pages',
                    'tag_id' => 2,
                    'other_id' => 405,
                ],
                [
                    'module' => 'Pages',
                    'tag_id' => 2,
                    'other_id' => 406,
                ],
                [
                    'module' => 'Faq',
                    'tag_id' => 1,
                    'other_id' => 1,
                ],
            ]
        );
    }
}
