<?php

namespace Backend\Modules\MediaLibrary\Manager;

class ExtensionManager
{
    /** @var array */
    protected $audioExtensions;

    /** @var array */
    protected $fileExtensions;

    /** @var array */
    protected $imageExtensions;

    /** @var array */
    protected $movieExtensions;

    /**
     * @param array $imageExtensions
     * @param array $fileExtensions
     * @param array $movieExtensions
     * @param array $audioExtensions
     */
    public function __construct(
        array $imageExtensions,
        array $fileExtensions,
        array $movieExtensions,
        array $audioExtensions
    ) {
        $this->imageExtensions = $imageExtensions;
        $this->fileExtensions = $fileExtensions;
        $this->movieExtensions = $movieExtensions;
        $this->audioExtensions = $audioExtensions;
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        return array_merge(
            $this->imageExtensions,
            $this->fileExtensions,
            $this->movieExtensions,
            $this->audioExtensions
        );
    }

    /**
     * @return array
     */
    public function getAudioExtensions(): array
    {
        return $this->audioExtensions;
    }

    /**
     * @return array
     */
    public function getFileExtensions(): array
    {
        return $this->fileExtensions;
    }

    /**
     * @return array
     */
    public function getImageExtensions(): array
    {
        return $this->imageExtensions;
    }

    /**
     * @return array
     */
    public function getMovieExtensions(): array
    {
        return $this->movieExtensions;
    }
}
