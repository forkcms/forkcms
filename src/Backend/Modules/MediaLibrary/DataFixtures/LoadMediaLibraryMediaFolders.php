<?php

namespace Backend\Modules\MediaLibrary\DataFixtures;

class LoadMediaLibraryMediaFolders
{
    /**
     * @param \SpoonDatabase $database
     */
    public function load(\SpoonDatabase $database)
    {
        $database->insert(
            'MediaFolder',
            array(
                'userId' => 1,
                'name' => 'cars',
                'createdOn' => new \DateTime(),
                'editedOn' => new \DateTime(),
                'parentMediaFolderId' => null,
            )
        );

        $parentId = $database->getVar(
            'SELECT i.id
             FROM MediaFolder AS i
             WHERE i.name = "cars"
             LIMIT 1'
        );

        $database->insert(
            'MediaFolder',
            array(
                'userId' => 1,
                'name' => 'audi',
                'createdOn' => new \DateTime(),
                'editedOn' => new \DateTime(),
                'parentMediaFolderId' => $parentId,
            )
        );

        $database->insert(
            'MediaFolder',
            array(
                'userId' => 1,
                'name' => 'bmw',
                'createdOn' => new \DateTime(),
                'editedOn' => new \DateTime(),
                'parentMediaFolderId' => $parentId,
            )
        );
    }
}
