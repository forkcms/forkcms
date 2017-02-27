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
        $movieId = trim(\SpoonFilter::getPostValue('id', null, '', 'string'));
        $movieTitle = trim(\SpoonFilter::getPostValue('title', null, '', 'string'));

        // Mime not valid
        if ($movieService === null) {
            $this->throwOutputError('MediaMovieSourceIsRequired');
        // External video id not valid
        } elseif ($movieId === '') {
            $this->throwOutputError('MediaMovieIdIsRequired');
        // Title not valid
        } elseif ($movieTitle === '') {
            $this->throwOutputError('MediaMovieTitleIsRequired');
        // Movie url (= externalVideoId) already exists in our repository
        } elseif ($this->get('media_library.repository.item')->existsOneByUrl($movieId)) {
            $this->throwOutputError('MediaMovieIdAlreadyExists');
        // Not already exists
        } else {
            /** @var CreateMediaItemFromMovieUrl $createMediaItem */
            $createMediaItemFromMovieUrl = new CreateMediaItemFromMovieUrl(
                $movieService,
                $movieId,
                $movieTitle,
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
     * @return MediaFolder|null
     */
    protected function getMediaFolder()
    {
        // Define id
        $id = \SpoonFilter::getPostValue('folder_id', null, null, 'int');

        // We have a folder
        if ($id !== null) {
            try {
                /** @var MediaFolder */
                return $this->get('media_library.repository.folder')->getOneById($id);
            } catch (\Exception $e) {
                $this->throwOutputError('ParentNotExists');
            }
        }

        return null;
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
        return trim(\SpoonFilter::getPostValue(
            'mime',
            MediaItem::getMimesForMovie(),
            null,
            'string'
        ));
    }
}
