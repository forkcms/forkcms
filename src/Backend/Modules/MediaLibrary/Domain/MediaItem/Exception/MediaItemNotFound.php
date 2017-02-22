<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem\Exception;

class MediaItemNotFound extends \Exception
{
    public static function forEmptyId()
    {
        return new \Exception('The id you have given is null');
    }

    public static function forId($id)
    {
        return new \Exception('Can\'t find a MediaItem with id = "' . $id . '"".');
    }
}
