<?php

namespace Backend\Modules\MediaLibrary\Ajax;

use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use App\Component\Locale\BackendLanguage;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\Exception\MediaFolderNotFound;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Command\CreateMediaItemFromMovieUrl;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaItem\StorageType;
use App\Exception\AjaxExitException;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

/**
 * This AJAX-action will add a new MediaItem movie.
 */
class MediaItemAddMovie extends BackendBaseAJAXAction
{
    public function execute(): void
    {
        parent::execute();

        /** @var CreateMediaItemFromMovieUrl $createMediaItemFromMovieUrl */
        $createMediaItemFromMovieUrl = $this->createMovieMediaItem();

        // Output success message
        $this->output(
            Response::HTTP_OK,
            $createMediaItemFromMovieUrl->getMediaItem(),
            BackendLanguage::msg('MediaUploadedSuccessful')
        );
    }

    private function createMovieMediaItem(): CreateMediaItemFromMovieUrl
    {
        /** @var CreateMediaItemFromMovieUrl $createMediaItem */
        $createMediaItemFromMovieUrl = new CreateMediaItemFromMovieUrl(
            $this->getMovieStorageType(),
            $this->getMovieId(),
            $this->getMovieTitle(),
            $this->getMediaFolder(),
            BackendAuthentication::getUser()->getUserId()
        );

        // Handle the MediaItem create
        $this->get('command_bus')->handle($createMediaItemFromMovieUrl);

        return $createMediaItemFromMovieUrl;
    }

    protected function getMovieId(): string
    {
        $movieId = trim($this->getRequest()->request->get('id'));

        // Movie id not null
        if ($movieId === null) {
            throw new AjaxExitException(BackendLanguage::err('MediaMovieIdIsRequired'));
        }

        // Movie url (= externalVideoId) already exists in our repository
        if ($this->get('media_library.repository.item')->existsOneByUrl((string) $movieId)) {
            throw new AjaxExitException(BackendLanguage::err('MediaMovieIdAlreadyExists'));
        }

        return $movieId;
    }

    protected function getMovieTitle(): string
    {
        $movieTitle = trim($this->getRequest()->request->get('title'));

        // Title not valid
        if ($movieTitle === null) {
            throw new AjaxExitException(BackendLanguage::err('MediaMovieTitleIsRequired'));
        }

        return $movieTitle;
    }

    protected function getMediaFolder(): MediaFolder
    {
        $id = $this->getRequest()->request->getInt('folder_id');

        if ($id === 0) {
            throw new AjaxExitException(BackendLanguage::err('MediaFolderIsRequired'));
        }

        try {
            /** @var MediaFolder */
            return $this->get('media_library.repository.folder')->findOneById($id);
        } catch (MediaFolderNotFound $mediaFolderNotFound) {
            throw new AjaxExitException(BackendLanguage::err('ParentNotExists'));
        }
    }

    protected function getMovieStorageType(): StorageType
    {
        $movieStorageType = $this->getRequest()->request->get('storageType');

        if ($movieStorageType === null
            || !in_array(
                (string) $movieStorageType,
                StorageType::POSSIBLE_VALUES_FOR_MOVIE,
                true
            )
        ) {
            throw new AjaxExitException(BackendLanguage::err('MovieStorageTypeNotExists'));
        }

        try {
            return StorageType::fromString($movieStorageType);
        } catch (InvalidArgumentException $invalidArgumentException) {
            throw new AjaxExitException(BackendLanguage::err('MovieStorageTypeNotExists'));
        }
    }
}
