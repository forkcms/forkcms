<?php

namespace App\Backend\Modules\MediaGalleries;

use App\Backend\Core\Engine\Base\Config as BackendBaseConfig;

/**
 * This is the configuration-object for the media galleries module
 */
class Config extends BackendBaseConfig
{
    /**
     * The default action
     *
     * @var string
     */
    protected $defaultAction = 'MediaGalleryIndex';
}
