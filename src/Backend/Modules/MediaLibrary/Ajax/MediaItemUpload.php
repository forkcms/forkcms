<?php

namespace Backend\Modules\MediaLibrary\Ajax;

use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Core\Language\Language;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Command\CreateMediaItemFromLocalStorageType;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Event\MediaItemCreated;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;
use Symfony\Component\Filesystem\Filesystem;
use Backend\Modules\MediaLibrary\Component\UploadHandler;

/**
 * This AJAX-action is being used to upload new MediaItem items and save them into to the database.
 */
class MediaItemUpload extends BackendBaseAJAXAction
{
    // override existing media
    const OVERRIDE_EXISTING = false;

    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

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
        // Include the upload handler class
        $uploader = new UploadHandler();
        // Specify the list of valid extensions, ex. array("jpeg", "xml", "bmp")
        $uploader->allowedExtensions = array(); // all files types allowed by default
        // Specify max file size in bytes.
        $uploader->sizeLimit = null;
        // Specify the input name set in the javascript.
        $uploader->inputName = "qqfile"; // matches Fine Uploader's default inputName value by default
        // If you want to use the chunking/resume feature, specify the folder to temporarily save parts.
        $uploader->chunksFolder = "chunks";
        //$method = $_SERVER["REQUEST_METHOD"];
        $method = $this->getRequestMethod();

        // Determine whether we are dealing with a regular ol' XMLHttpRequest, or
        // an XDomainRequest
        $_HEADERS = $this->parseRequestHeaders();
        $iframeRequest = false;

        if (!isset($_HEADERS['X-Requested-With']) || $_HEADERS['X-Requested-With'] != "XMLHttpRequest") {
            $iframeRequest = true;
        }

        /*
         * handle the preflighted OPTIONS request. Needed for CORS operation.
         */
        if ($method == "OPTIONS") {
            $this->handlePreflight();
        /*
         * handle a POST
         */
        } elseif ($method == "POST") {
            $this->handleCorsRequest();
            header("Content-Type: text/plain");

            // Assumes you have a chunking.success.endpoint set to point here with a query parameter of "done".
            // For example: /myserver/handlers/endpoint.php?done
            if (isset($_GET["done"])) {
                $result = $uploader->combineChunks("files");
            // Handles upload requests
            } else {
                // Define upload dir
                $uploadDir = MediaItem::getUploadRootDir() . '/' . $this->get('media_library.manager.file')->getNextShardingFolder();

                // Generate folder if not exists
                $this->get('media_library.manager.file')->createFolder($uploadDir);

                // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
                $result = $uploader->handleUpload($uploadDir);

                // To return a name used for uploaded file you can use the following line.
                $result["uploadName"] = $uploader->getUploadName();

                // Generate filename which doesn't exist yet in our media library
                $newName = $this->get('media_library.manager.file')->getUniqueFileName(
                    $uploadDir,
                    $result['uploadName']
                );

                $fs = new Filesystem();
                if ($fs->exists($uploadDir . '/' . $result['uuid'] . '/' . $result['uploadName'])) {
                    // Move file to correct folder
                    $fs->rename(
                        $uploadDir . '/' . $result['uuid'] . '/' . $result['uploadName'],
                        $uploadDir . '/' . $newName
                    );

                    // Remove the old folder
                    $fs->remove($uploadDir . '/' . $result['uuid']);
                }

                /** @var CreateMediaItemFromLocalStorageType $createMediaItem */
                $createMediaItemFromLocalSource = new CreateMediaItemFromLocalStorageType(
                    $uploadDir . '/' . $newName,
                    $this->getMediaFolder(),
                    BackendAuthentication::getUser()->getUserId()
                );

                // Handle the MediaItem create
                $this->get('command_bus')->handle($createMediaItemFromLocalSource);
                $this->get('event_dispatcher')->dispatch(
                    MediaItemCreated::EVENT_NAME,
                    new MediaItemCreated($createMediaItemFromLocalSource->getMediaItem())
                );

                $resultData = json_encode(
                    array_merge(
                        $result,
                        $createMediaItemFromLocalSource->getMediaItem()->__toArray()
                    )
                );

                // iframe uploads require the content-type to be 'text/html' and
                // return some JSON along with self-executing javascript (iframe.ss.response)
                // that will parse the JSON and pass it along to Fine Uploader via
                // window.postMessage
                if ($iframeRequest === true) {
                    header("Content-Type: text/html");
                    $resultData .= "<script src='http://{{SERVER_URL}}/{{FINE_UPLOADER_FOLDER}}/iframe.xss.response.js'></script>";
                }
                echo $resultData;
                exit();
            }
        } else {
            header("HTTP/1.0 405 Method Not Allowed");
        }
    }

    // This will retrieve the "intended" request method.  Normally, this is the
    // actual method of the request.  Sometimes, though, the intended request method
    // must be hidden in the parameters of the request.  For example, when attempting to
    // send a DELETE request in a cross-origin environment in IE9 or older, it is not
    // possible to send a DELETE request.  So, we send a POST with the intended method,
    // DELETE, in a "_method" parameter.
    private function getRequestMethod()
    {
        global $HTTP_RAW_POST_DATA;
        // This should only evaluate to true if the Content-Type is undefined
        // or unrecognized, such as when XDomainRequest has been used to
        // send the request.
        if (isset($HTTP_RAW_POST_DATA)) {
            parse_str($HTTP_RAW_POST_DATA, $_POST);
        }

        if (isset($_POST["_method"]) && $_POST["_method"] != null) {
            return $_POST["_method"];
        }

        return $_SERVER["REQUEST_METHOD"];
    }

    private function parseRequestHeaders()
    {
        $headers = array();
        foreach ($_SERVER as $key => $value) {
            if (substr($key, 0, 5) <> 'HTTP_') {
                continue;
            }
            $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
            $headers[$header] = $value;
        }
        return $headers;
    }

    /**
     * Get MediaFolder
     *
     * @return MediaFolder
     */
    private function getMediaFolder(): MediaFolder
    {
        // Define id
        $id = $this->get('request')->query->get('folder_id');

        if ($id === null) {
            $this->throwOutputError('MediaFolderIsRequired');
        }

        try {
            /** @var MediaFolder */
            return $this->get('media_library.repository.folder')->getOneById((int) $id);
        } catch (\Exception $e) {
            $this->throwOutputError('NotExistingMediaFolder');
        }
    }

    private function handleCorsRequest()
    {
        header("Access-Control-Allow-Origin: *");
    }

    /*
     * handle pre-flighted requests. Needed for CORS operation
     */
    private function handlePreflight()
    {
        $this->handleCorsRequest();
        header("Access-Control-Allow-Methods: POST, DELETE");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, X-Requested-With, Cache-Control");
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
}
