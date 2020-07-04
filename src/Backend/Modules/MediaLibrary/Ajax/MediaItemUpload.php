<?php

namespace Backend\Modules\MediaLibrary\Ajax;

use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Core\Language\Language;
use Backend\Modules\MediaLibrary\Component\StorageProvider\LocalStorageProvider;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\Exception\MediaFolderNotFound;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolderRepository;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Command\CreateMediaItemFromLocalStorageType;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use Backend\Modules\MediaLibrary\Component\UploadHandler;
use Backend\Modules\MediaLibrary\Manager\ExtensionManager;
use Backend\Modules\MediaLibrary\Manager\FileManager;
use Backend\Modules\MediaLibrary\Manager\MimeTypeManager;
use Common\Exception\AjaxExitException;
use Common\Exception\RedirectException;
use SimpleBus\Message\Bus\MessageBus;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * This AJAX-action is being used to upload new MediaItem items and save them into to the database.
 */
class MediaItemUpload extends BackendBaseAJAXAction
{
    /** @var Response */
    protected $response;

    /** @var Request */
    private $request;

    /** @var LocalStorageProvider */
    private $localStorageProvider;

    /** @var FileManager */
    private $fileManager;

    /** @var ExtensionManager */
    private $extensionManager;

    /** @var MimeTypeManager */
    private $mimeTypeManager;

    /** @var MediaFolderRepository */
    private $mediaFolderRepository;

    /** @var MessageBus */
    private $commandBus;

    /** @var UploadHandler */
    private $uploadHandler;

    /** @var string */
    private $uploadDirectory;

    public function setKernel(KernelInterface $kernel = null): void
    {
        parent::setKernel($kernel);

        if ($kernel === null) {
            return;
        }

        $this->localStorageProvider = $this->get('media_library.storage.local');
        $this->fileManager = $this->get('media_library.manager.file');
        $this->extensionManager = $this->get('media_library.manager.extension');
        $this->mimeTypeManager = $this->get('media_library.manager.mime_type');
        $this->mediaFolderRepository = $this->get('media_library.repository.folder');
        $this->commandBus = $this->get('command_bus');
    }

    /**
     * PHP Server-Side Example for Fine Uploader (traditional endpoint handler).
     * Maintained by Widen Enterprises.
     *
     * This example:
     *  - handles chunked and non-chunked requests
     *  - supports the concurrent chunking feature
     *  - assumes all upload requests are multipart encoded
     *  - handles delete requests
     *  - handles cross-origin environments
     *
     * Follow these steps to get up and running with Fine Uploader in a PHP environment:
     *
     * 1. Setup your client-side code, as documented on http://docs.fineuploader.com.
     *
     * 2. Copy this file and handler.php to your server.
     *
     * 3. Ensure your php.ini file contains appropriate values for
     *    max_input_time, upload_max_filesize and post_max_size.
     *
     * 4. Ensure your "chunks" and "files" folders exist and are writable.
     *    "chunks" is only needed if you have enabled the chunking feature client-side.
     *
     * 5. If you have chunking enabled in Fine Uploader, you MUST set a value for the `chunking.success.endpoint` option.
     *    This will be called by Fine Uploader when all chunks for a file have been successfully uploaded, triggering the
     *    PHP server to combine all parts into one file. This is particularly useful for the concurrent chunking feature,
     *    but is now required in all cases if you are making use of this PHP example.
     */
    public function execute(): void
    {
        parent::execute();

        $this->request = $this->getRequest();
        $this->response = $this->createResponse();
        $this->uploadHandler = $this->createUploader();
        $this->uploadDirectory = $this->getUploadDirectory();
        $this->combineChunksAfterReceivingThemAll();

        $result = $this->uploadHandler->handleUpload($this->uploadDirectory);

        if (array_key_exists('error', $result)
            || (array_key_exists('success', $result)
                && !$this->request->query->has('done')
                && $this->request->request->getInt('qqtotalparts', 1) > 1
            )) {
            $this->sendResponseForResult($result);
        }

        // To return a name used for uploaded file you can use the following line.
        $result['uploadName'] = $this->uploadHandler->getUploadName();

        // Generate filename which doesn't exist yet in our media library
        $newName = $this->fileManager->getUniqueFileName($this->uploadDirectory, $result['uploadName']);

        $tempUploadPath = $this->uploadDirectory . '/' . $result['uuid'] . '/' . $result['uploadName'];
        if ($this->fileManager->exists($tempUploadPath)) {
            // Move file to correct folder
            $this->fileManager->rename($tempUploadPath, $this->uploadDirectory . '/' . $newName);

            // Remove the old folder
            $this->fileManager->deleteFolder($this->uploadDirectory . '/' . $result['uuid']);
        }

        /** @var CreateMediaItemFromLocalStorageType $createMediaItem */
        $createMediaItemFromLocalSource = new CreateMediaItemFromLocalStorageType(
            $this->uploadDirectory . '/' . $newName,
            $this->getMediaFolder(),
            BackendAuthentication::getUser()->getUserId()
        );

        $this->commandBus->handle($createMediaItemFromLocalSource);

        $this->sendResponseForResult(
            array_merge(
                $result,
                $createMediaItemFromLocalSource->getMediaItem()->jsonSerialize()
            )
        );
    }

    private function sendResponseForResult(array $result): void
    {
        $this->response->headers->set('Content-Type', 'text/json');
        $this->response->setContent(json_encode($result));

        throw new RedirectException('media item upload', $this->response);
    }

    /**
     * This will retrieve the "intended" request method.  Normally, this is the
     *  actual method of the request.  Sometimes, though, the intended request method
     *  must be hidden in the parameters of the request.  For example, when attempting to
     *  send a DELETE request in a cross-origin environment in IE9 or older, it is not
     *  possible to send a DELETE request.  So, we send a POST with the intended method,
     *  DELETE, in a "_method" parameter.
     */
    private function getRequestMethod(): string
    {
        if ($this->request->request->get('method') !== null
            && $this->request->request->get('_method') !== null) {
            return $this->request->request->get('_method');
        }

        return $this->request->server->get('REQUEST_METHOD');
    }

    private function getMediaFolder(): MediaFolder
    {
        $id = $this->request->query->getInt('folder_id');

        if ($id === 0) {
            throw new AjaxExitException(Language::err('MediaFolderIsRequired'));
        }

        try {
            return $this->mediaFolderRepository->findOneById($id);
        } catch (MediaFolderNotFound $mediaFolderNotFound) {
            throw new AjaxExitException(Language::err('NonExistingMediaFolder'));
        }
    }

    private function createUploader(): UploadHandler
    {
        $uploader = new UploadHandler($this->request, $this->fileManager);
        // Specify the list of valid extensions, ex. array("jpeg", "xml", "bmp")
        $uploader->allowedExtensions = $this->extensionManager->getAll();
        $uploader->allowedMimeTypes = $this->mimeTypeManager->getAll();
        // Specify max file size in bytes.
        $uploader->sizeLimit = null;
        // Specify the input name set in the javascript.
        $uploader->inputName = 'qqfile'; // matches Fine Uploader's default inputName value by default
        // If you want to use the chunking/resume feature, specify the folder to temporarily save parts.
        $uploader->chunksFolder = FRONTEND_FILES_PATH . '/MediaLibrary/uploads/chunks';

        return $uploader;
    }

    private function createResponse(): Response
    {
        $response = new Response();
        $response->headers->set('Access-Control-Allow-Origin', '*');

        $method = $this->getRequestMethod();

        switch ($method) {
            case 'POST':
                $response->headers->set('Content-Type', 'text/plain');

                return $response;

            case 'OPTIONS': // handle the preflighted OPTIONS request. Needed for CORS operation.
                $response->headers->set('Access-Control-Allow-Methods', 'POST');
                $response->headers->set('Access-Control-Allow-Credentials', 'true');
                $response->headers->set(
                    'Access-Control-Allow-Headers',
                    'Content-Type, X-Requested-With, Cache-Control'
                );
                break;

            default:
                $response->setStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);
                break;
        }

        throw new RedirectException('exit', $response);
    }

    private function getUploadDirectory(): string
    {
        $uploadDirectory = $this->localStorageProvider->getUploadRootDir() . '/'
                           . $this->fileManager->getNextShardingFolder();
        // Generate folder if not exists
        $this->fileManager->createFolder($uploadDirectory);

        return $uploadDirectory;
    }

    private function combineChunksAfterReceivingThemAll(): void
    {
        if ($this->request->query->get('done') === null) {
            return;
        }

        $combineChunksResult = $this->uploadHandler->combineChunks($this->uploadDirectory);
        if ($combineChunksResult['success'] === false) {
            $this->sendResponseForResult($combineChunksResult);
        }
    }
}
