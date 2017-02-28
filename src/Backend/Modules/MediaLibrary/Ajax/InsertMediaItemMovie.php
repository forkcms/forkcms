<?php

namespace Backend\Modules\MediaLibrary\Ajax;

use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Core\Language\Language;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Command\CreateMediaItemFromMovieUrl;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Event\MediaItemCreated;

/**
 * This AJAX-action will insert a new MediaFolder.
 */
class InsertMediaItemMovie extends BackendBaseAJAXAction
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        /** @var MediaFolder $mediaFolder */
        $mediaFolder = $this->getMediaFolder();

        /** @var string|null $movieService */
        $movieService = $this->getMovieService();

        // Define parameters
        $movieId = trim($this->get('request')->request->get('id'));
        $movieTitle = trim($this->get('request')->request->get('title'));

        // Mime not valid
        if ($movieService === null) {
            $this->throwOutputError('MediaMovieSourceIsRequired');
        // External video id not valid
        } elseif ($movieId === null) {
            $this->throwOutputError('MediaMovieIdIsRequired');
        // Title not valid
        } elseif ($movieTitle === null) {
            $this->throwOutputError('MediaMovieTitleIsRequired');
        // Movie url (= externalVideoId) already exists in our repository
        } elseif ($this->get('media_library.repository.item')->existsOneByUrl((string) $movieId)) {
            $this->throwOutputError('MediaMovieIdAlreadyExists');
        // Not already exists
        } else {
            /** @var CreateMediaItemFromMovieUrl $createMediaItem */
            $createMediaItemFromMovieUrl = new CreateMediaItemFromMovieUrl(
                $movieService,
                (string) $movieId,
                (string) $movieTitle,
                $mediaFolder,
                BackendAuthentication::getUser()->getUserId()
            );

            // Handle the MediaItem create
            $this->get('command_bus')->handle($createMediaItemFromMovieUrl);
            $this->get('event_dispatcher')->dispatch(
                MediaItemCreated::EVENT_NAME,
                new MediaItemCreated($createMediaItemFromMovieUrl->getMediaItem())
            );

            // Output success message
            $this->output(
                self::OK,
                $createMediaItemFromMovieUrl->getMediaItem()->__toArray(),
                Language::msg('MediaUploadedSuccessful')
            );
        }
    }

    /**
     * Get MediaFolder
     *
     * @return MediaFolder
     */
    protected function getMediaFolder()
    {
        // Define id
        $id = $this->get('request')->request->get('folder_id');

        if ($id === null) {
            $this->throwOutputError('MediaFolderIsRequired');
        }

        try {
            /** @var MediaFolder */
            return $this->get('media_library.repository.folder')->getOneById((int) $id);
        } catch (\Exception $e) {
            $this->throwOutputError('ParentNotExists');
        }
    }

    /**
     * @param $error
     */
    private function throwOutputError(string $error)
    {
        // Throw output error
        $this->output(
            self::BAD_REQUEST,
            null,
            Language::err($error)
        );
    }

    /**
     * @return string
     */
    protected function getMovieService()
    {
        $movieService = $this->get('request')->request->get('mime');

        if ($movieService === null || !in_array((string) $movieService, MediaItem::getMimesForMovie())) {
            $this->throwOutputError('MovieServiceNotExists');
        }

        return (string) $movieService;
    }
}
