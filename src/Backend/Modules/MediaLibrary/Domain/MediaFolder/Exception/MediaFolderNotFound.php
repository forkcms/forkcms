<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaFolder\Exception;

class MediaFolderNotFound extends \Exception
{
    public static function forEmptyId()
    {
        return new \Exception('The id you have given is null');
    }

    public static function forId(int $id)
    {
        return new \Exception('Can\'t find a MediaFolder with id = "' . $id . '"".');
    }
}
