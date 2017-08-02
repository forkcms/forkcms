<?php

namespace Backend\Modules\MediaLibrary\Ajax;

use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Core\Language\Language;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\Exception\MediaFolderNotFound;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Command\CreateMediaItemFromLocalStorageType;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use Backend\Modules\MediaLibrary\Component\UploadHandler;
use Common\Exception\AjaxExitException;
use Common\Exception\RedirectException;
use Symfony\Component\HttpFoundation\Response;

/**
 * This AJAX-action is being used to upload new MediaItem items and save them into to the database.
 */
class MediaItemUpload extends BackendBaseAJAXAction
{
    // override existing media
    const OVERRIDE_EXISTING = false;

    /** @var Response */
    protected $response;

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

        // Include the upload handler class
        $uploader = new UploadHandler($this->getRequest(), $this->get('media_library.manager.file'));
        // Specify the list of valid extensions, ex. array("jpeg", "xml", "bmp")
        $uploader->allowedExtensions = $this->get('media_library.manager.extension')->getAll();
        $uploader->allowedMimeTypes = $this->get('media_library.manager.mime_type')->getAll();
        // Specify max file size in bytes.
        $uploader->sizeLimit = null;
        // Specify the input name set in the javascript.
        $uploader->inputName = 'qqfile'; // matches Fine Uploader's default inputName value by default
        // If you want to use the chunking/resume feature, specify the folder to temporarily save parts.
        $uploader->chunksFolder = 'chunks';

        $this->response = new Response();

        $method = $this->getRequestMethod();

        /*
         * handle the preflighted OPTIONS request. Needed for CORS operation.
         */
        if ($method === 'OPTIONS') {
            $this->handlePreflight();
            $this->response->send();
            exit();
        }

        if ($method !== 'POST') {
            $this->response->setStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);
            $this->response->send();
            exit();
        }

        $this->handleCorsRequest();
        $this->response->headers->set('Content-Type', 'text/plain');

        // Assumes you have a chunking.success.endpoint set to point here with a query parameter of "done".
        // For example: /myserver/handlers/endpoint.php?done
        if ($this->getRequest()->query->get('done') !== null) {
            $this->response->setContent($uploader->combineChunks('files'));
            $this->response->send();
            exit();
        }

        // Define upload dir
        $uploadDir = $this->get('media_library.storage.local')->getUploadRootDir() . '/' . $this->get(
            'media_library.manager.file'
        )->getNextShardingFolder();

        // Generate folder if not exists
        $this->get('media_library.manager.file')->createFolder($uploadDir);

        // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
        $result = $uploader->handleUpload($uploadDir);

        if (array_key_exists('error', $result)) {
            $this->sendResponseForResult($result);
        }

        // To return a name used for uploaded file you can use the following line.
        $result['uploadName'] = $uploader->getUploadName();

        // Generate filename which doesn't exist yet in our media library
        $newName = $this->get('media_library.manager.file')->getUniqueFileName(
            $uploadDir,
            $result['uploadName']
        );

        $tempUploadPath = $uploadDir . '/' . $result['uuid'] . '/' . $result['uploadName'];
        if ($this->get('media_library.manager.file')->exists($tempUploadPath)) {
            // Move file to correct folder
            $this->get('media_library.manager.file')->rename($tempUploadPath, $uploadDir . '/' . $newName);

            // Remove the old folder
            $this->get('media_library.manager.file')->deleteFolder($uploadDir . '/' . $result['uuid']);
        }

        /** @var CreateMediaItemFromLocalStorageType $createMediaItem */
        $createMediaItemFromLocalSource = new CreateMediaItemFromLocalStorageType(
            $uploadDir . '/' . $newName,
            $this->getMediaFolder(),
            BackendAuthentication::getUser()->getUserId()
        );

        // Handle the MediaItem create
        $this->get('command_bus')->handle($createMediaItemFromLocalSource);

        $this->sendResponseForResult(
            array_merge(
                $result,
                $createMediaItemFromLocalSource->getMediaItem()->jsonSerialize()
            )
        );
    }

    private function sendResponseForResult(array $result): void
    {
        // Determine whether we are dealing with a regular ol' XMLHttpRequest, or an XDomainRequest
        $iframeRequest = $this->getRequest()->headers->get('x-requested-with') !== 'XMLHttpRequest';

        $resultData = json_encode($result);
        // iframe uploads require the content-type to be 'text/html' and
        // return some JSON along with self-executing javascript (iframe.ss.response)
        // that will parse the JSON and pass it along to Fine Uploader via
        // window.postMessage
        if ($iframeRequest === true) {
            $this->response->headers->set('Content-Type', 'text/html');
            $resultData .= "<script src='http://{{SERVER_URL}}/{{FINE_UPLOADER_FOLDER}}/iframe.xss.response.js'></script>";
        }

        $this->response->setContent($resultData);

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
        if ($this->getRequest()->request->get('method') !== null
            && $this->getRequest()->request->get('_method') !== null
        ) {
            return $this->getRequest()->request->get('_method');
        }

        return $this->getRequest()->server->get('REQUEST_METHOD');
    }

    private function getMediaFolder(): MediaFolder
    {
        // Define id
        $id = $this->getRequest()->query->getInt('folder_id');

        if ($id === 0) {
            throw new AjaxExitException(Language::err('MediaFolderIsRequired'));
        }

        try {
            /** @var MediaFolder */
            return $this->get('media_library.repository.folder')->findOneById($id);
        } catch (MediaFolderNotFound $mediaFolderNotFound) {
            throw new AjaxExitException(Language::err('NotExistingMediaFolder'));
        }
    }

    private function handleCorsRequest(): void
    {
        $this->response->headers->set('Access-Control-Allow-Origin', '*');
    }

    /**
     * handle pre-flighted requests. Needed for CORS operation
     */
    private function handlePreflight(): void
    {
        $this->handleCorsRequest();
        $this->response->headers->set('Access-Control-Allow-Methods', 'POST, DELETE');
        $this->response->headers->set('Access-Control-Allow-Credentials', 'true');
        $this->response->headers->set('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With, Cache-Control');
    }
}
