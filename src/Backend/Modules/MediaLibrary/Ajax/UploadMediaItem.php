<?php

namespace Backend\Modules\MediaLibrary\Ajax;

use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Core\Language\Language;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Command\CreateMediaItemFromSource;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Event\MediaItemCreated;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;

/**
 * This AJAX-action is being used to upload new MediaItem items and save them into to the database.
 */
class UploadMediaItem extends BackendBaseAJAXAction
{
    // override existing media
    const OVERRIDE_EXISTING = false;

    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        try {
            // 60 minutes execution time
            set_time_limit(60 * 60);
        } catch (\Exception $e) {
            // Do nothing
        }

        // increase memory limit
        ini_set('memory_limit', '128M');

        $contentType = null;

        // define folder id
        $folderId = trim(\SpoonFilter::getPostValue(
            'folder_id',
            null,
            '',
            'int'
        ));

        // define destination URL
        $destinationURL = trim(\SpoonFilter::getPostValue(
            'filename',
            null,
            '',
            'string'
        ));

        // redefine destination URL as unique URL
        $destinationURL = $this->get('media_library.manager.file')->getUniqueURL(
            $destinationURL,
            self::OVERRIDE_EXISTING
        );

        // define destination source path
        $destinationSourcePath = MediaItem::getUploadRootDir() . '/' . $destinationURL;

        // create folder if not exists
        if (!\SpoonDirectory::exists(dirname($destinationSourcePath))) {
            \SpoonDirectory::create(dirname($destinationSourcePath));
        }

        // HTTP headers for no cache etc
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');

        // get parameters
        $chunk = isset($_REQUEST['chunk']) ? (int) $_REQUEST['chunk'] : 0;
        $chunks = isset($_REQUEST['chunks']) ? (int) $_REQUEST['chunks'] : 0;

        // look for the content type header
        if (isset($_SERVER['HTTP_CONTENT_TYPE'])) {
            $contentType = $_SERVER['HTTP_CONTENT_TYPE'];
        }
        if (isset($_SERVER['CONTENT_TYPE'])) {
            $contentType = $_SERVER['CONTENT_TYPE'];
        }

        // handle multipart files
        if (strpos($contentType, 'multipart') !== false) {
            // upload temp file
            if (isset($_FILES['file']['tmp_name'])
                && is_uploaded_file($_FILES['file']['tmp_name'])
            ) {
                // open temp file
                $out = fopen($destinationSourcePath, $chunk == 0 ? 'wb' : 'ab');

                // write file
                if ($out) {
                    // read binary input stream and append it to temp file
                    $in = fopen($_FILES['file']['tmp_name'], 'rb');

                    // write
                    if ($in) {
                        while ($buff = fread($in, 4096)) {
                            fwrite($out, $buff);
                        }
                    // error opening input stream
                    } else {
                        $this->output(
                            self::BAD_REQUEST,
                            null,
                            'Failed to open input stream.'
                        );
                    }

                    fclose($in);
                    fclose($out);

                    try {
                        unlink($_FILES['file']['tmp_name']);
                    } catch (\Exception $e) {
                        // Do nothing
                    }

                    // only handle uploaded file when chunk is ready
                    if ($chunks === $chunk || ($chunks > 1 && ($chunk == ($chunks - 1)))) {
                        // handle uploaded media item
                        $this->handleUploadedMediaItem(
                            $destinationURL,
                            $folderId
                        );
                    }
                // error can't write file
                } else {
                    $this->output(
                        self::BAD_REQUEST,
                        null,
                        'Failed to open output stream.'
                    );
                }
            // error when moving uploaded file
            } else {
                $this->output(
                    self::BAD_REQUEST,
                    null,
                    'Failed to move uploaded file.'
                );
            }
        // handle non multipart uploads older WebKit versions didn't support multipart in HTML5
        } else {
            // open temp file
            $out = fopen($destinationSourcePath, $chunk == 0 ? 'wb' : 'ab');

            // write temp file
            if ($out) {
                // read binary input stream and append it to temp file
                $in = fopen('php://input', 'rb');

                if ($in) {
                    while ($buff = fread($in, 4096)) {
                        fwrite($out, $buff);
                    }
                // error opening input stream
                } else {
                    $this->output(
                        self::BAD_REQUEST,
                        null,
                        'Failed to open input stream.'
                    );
                }

                fclose($in);
                fclose($out);

                // handle uploaded media item
                $this->handleUploadedMediaItem(
                    $destinationURL,
                    $folderId
                );
            // error opening output stream
            } else {
                $this->output(
                    self::BAD_REQUEST,
                    null,
                    'Failed to open output stream.'
                );
            }
        }
    }

    /**
     * Handle the uploaded media item
     *
     * @param string $name The url for the new uploaded file
     * @param int[optional] $folderId The id of the folder where the media has been uploaded
     * @return json Returns the new media item as an object.
     */
    private function handleUploadedMediaItem(
        string $name,
        int $folderId
    ) {
        // Define source
        $source = MediaItem::getUploadRootDir() . '/' . $name;

        /** @var MediaFolder $mediaFolder */
        $mediaFolder = $this->getMediaFolder($folderId);

        /** @var CreateMediaItemFromSource $createMediaItem */
        $createMediaItemFromSource = new CreateMediaItemFromSource(
            $source,
            $mediaFolder,
            BackendAuthentication::getUser()->getUserId()
        );

        // Handle the MediaItem create
        $this->get('command_bus')->handle($createMediaItemFromSource);
        $this->get('event_dispatcher')->dispatch(
            MediaItemCreated::EVENT_NAME,
            new MediaItemCreated($createMediaItemFromSource->getMediaItem())
        );

        // set media auto_increment
        $this->get('fork.settings')->set(
            'MediaLibrary',
            'upload_auto_increment',
            $this->get('fork.settings')->get(
                'MediaLibrary',
                'upload_auto_increment',
                0
            ) + 1
        );

        $this->output(
            self::OK,
            $createMediaItemFromSource->getMediaItem()->__toArray(),
            Language::msg('MediaUploadedSuccessful')
        );
    }

    /**
     * @param int $id
     * @throws \Exception
     * @return MediaFolder
     */
    private function getMediaFolder(int $id)
    {
        try {
            /** @var MediaFolder */
            return $this->get('media_library.repository.folder')->getOneById($id);
        } catch (\Exception $e) {
            $this->output(
                self::BAD_REQUEST,
                null,
                Language::err('NotExistingMediaFolder')
            );
        }
    }
}
