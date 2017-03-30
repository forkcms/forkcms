<?php

namespace Backend\Modules\MediaLibrary\Manager;

class MimeTypeManager
{
    /** @var array */
    protected $audioMimeTypes;

    /** @var array */
    protected $fileMimeTypes;

    /** @var array */
    protected $imageMimeTypes;

    /** @var array */
    protected $movieMimeTypes;

    /**
     * @param array $imageMimeTypes
     * @param array $fileMimeTypes
     * @param array $movieMimeTypes
     * @param array $audioMimeTypes
     */
    public function __construct(
        array $imageMimeTypes,
        array $fileMimeTypes,
        array $movieMimeTypes,
        array $audioMimeTypes
    ) {
        $this->imageMimeTypes = $imageMimeTypes;
        $this->fileMimeTypes = $fileMimeTypes;
        $this->movieMimeTypes = $movieMimeTypes;
        $this->audioMimeTypes = $audioMimeTypes;
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        return array_merge(
            $this->imageMimeTypes,
            $this->fileMimeTypes,
            $this->movieMimeTypes,
            $this->audioMimeTypes
        );
    }

    /**
     * @return array
     */
    public function getAudioMimeTypes(): array
    {
        return $this->audioMimeTypes;
    }

    /**
     * @return array
     */
    public function getFileMimeTypes(): array
    {
        return $this->fileMimeTypes;
    }

    /**
     * @return array
     */
    public function getImageMimeTypes(): array
    {
        return $this->imageMimeTypes;
    }

    /**
     * @return array
     */
    public function getMovieMimeTypes(): array
    {
        return $this->movieMimeTypes;
    }
}
