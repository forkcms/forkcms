<?php

namespace Backend\Modules\MediaLibrary\Component;

use Backend\Modules\MediaLibrary\Manager\FileManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class UploadHandler
{
    public $allowedExtensions = [];
    public $allowedMimeTypes = [];
    public $sizeLimit = null;
    public $inputName = 'qqfile';
    public $chunksFolder = 'chunks';

    public $chunksCleanupProbability = 0.001; // Once in 1000 requests on avg
    public $chunksExpireIn = 604800; // One week

    /** @var string */
    protected $uploadName;

    /** @var Request */
    protected $request;

    /** @var FileManager */
    protected $fileManager;

    /**
     * @param Request $request
     * @param FileManager $fileManager
     */
    public function __construct(Request $request, FileManager $fileManager)
    {
        $this->request = $request;
        $this->fileManager = $fileManager;
    }

    /**
     * Get the original filename
     * @return string
     */
    public function getName(): string
    {
        $fileName = $this->request->request->get('qqfilename');
        if ($fileName !== null) {
            return $fileName;
        }

        /** @var UploadedFile|null $file */
        $file = $this->request->files->get($this->inputName);
        if ($file instanceof UploadedFile) {
            return $file->getClientOriginalName();
        }
    }

    /**
     * Get the name of the uploaded file
     */
    public function getUploadName(): string
    {
        return $this->uploadName;
    }

    /**
     * @param string $uploadDirectory
     * @param string|null $name
     * @return array
     */
    public function combineChunks(string $uploadDirectory, string $name = null): array
    {
        $uuid = $this->request->request->get('qquuid');
        if ($name === null) {
            $name = $this->getName();
        }
        $targetFolder = $this->chunksFolder . DIRECTORY_SEPARATOR . $uuid;
        $totalParts = $this->request->request->getInt('qqtotalparts', 1);

        $targetPath = join(DIRECTORY_SEPARATOR, [$uploadDirectory, $uuid, $name]);
        $this->uploadName = $name;

        if (!$this->fileManager->exists($targetPath)) {
            mkdir(dirname($targetPath), 0777, true);
        }
        $target = fopen($targetPath, 'wb');

        for ($i = 0; $i < $totalParts; $i++) {
            $chunk = fopen($targetFolder . DIRECTORY_SEPARATOR . $i, "rb");
            stream_copy_to_stream($chunk, $target);
            fclose($chunk);
        }

        // Success
        fclose($target);

        for ($i = 0; $i < $totalParts; $i++) {
            unlink($targetFolder . DIRECTORY_SEPARATOR . $i);
        }

        rmdir($targetFolder);

        if (!is_null($this->sizeLimit) && filesize($targetPath) > $this->sizeLimit) {
            unlink($targetPath);
            http_response_code(413);
            return ["success" => false, "uuid" => $uuid, "preventRetry" => true];
        }

        return ["success" => true, "uuid" => $uuid];
    }

    /**
     * Process the upload.
     *
     * @param string $uploadDirectory Target directory.
     * @param string $name Overwrites the name of the file.
     * @return array
     */
    public function handleUpload($uploadDirectory, $name = null)
    {
        $this->cleanupChunksIfNecessary();

        try {
            $this->checkMaximumSize();
            $this->checkUploadDirectory($uploadDirectory);
            $this->checkType();
            $file = $this->getFile();
            $size = $this->getSize($file);

            if (!is_null($this->sizeLimit) && $size > $this->sizeLimit) {
                return ['error' => 'File is too large.', 'preventRetry' => true];
            }

            $name = $this->getRedefinedName($name);
            $this->checkFileExtension($name);
            $this->checkFileMimeType($file);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }

        $uuid = $this->request->request->get('qquuid');

        // Chunked upload
        if ($this->request->request->getInt('qqtotalparts', 1) > 1) {
            $chunksFolder = $this->chunksFolder;
            $partIndex = $this->request->request->getInt('qqpartindex');

            if (!is_writable($chunksFolder) && !is_executable($uploadDirectory)) {
                return ['error' => "Server error. Chunks directory isn't writable or executable."];
            }

            $targetFolder = $this->chunksFolder . DIRECTORY_SEPARATOR . $uuid;

            if (!file_exists($targetFolder)) {
                mkdir($targetFolder, 0777, true);
            }

            $target = $targetFolder . '/' . $partIndex;
            $success = move_uploaded_file($file->getRealPath(), $target);

            return ["success" => $success, "uuid" => $uuid];
        // Non-chunked upload
        } else {
            $target = join(DIRECTORY_SEPARATOR, [$uploadDirectory, $uuid, $name]);

            if ($target) {
                $this->uploadName = basename($target);

                if (!is_dir(dirname($target))) {
                    mkdir(dirname($target), 0777, true);
                }
                if (move_uploaded_file($file->getRealPath(), $target)) {
                    return ['success'=> true, "uuid" => $uuid];
                }
            }

            return ['error'=> 'Could not save uploaded file.' . 'The upload was cancelled, or server error encountered'];
        }
    }

    /**
     * @throws \Exception
     */
    private function checkMaximumSize()
    {
        // Check that the max upload size specified in class configuration does not exceed size allowed by server config
        if ($this->toBytes(ini_get('post_max_size')) < $this->sizeLimit ||
            $this->toBytes(ini_get('upload_max_filesize')) < $this->sizeLimit
        ) {
            $neededRequestSize = max(1, $this->sizeLimit / 1024 / 1024) . 'M';

            throw new \Exception('Server error. Increase post_max_size and upload_max_filesize to ' . $neededRequestSize);
        }
    }

    /**
     * Determines whether a directory can be accessed.
     *
     * is_executable() is not reliable on Windows prior PHP 5.0.0
     *  (http://www.php.net/manual/en/function.is-executable.php)
     * The following tests if the current OS is Windows and if so, merely
     * checks if the folder is writable;
     * otherwise, it checks additionally for executable status (like before).
     *
     * @param string $uploadDirectory
     * @throws \Exception
     */
    private function checkUploadDirectory(string $uploadDirectory)
    {
        $isWin = $this->isWindows();
        $folderInaccessible = ($isWin) ? !is_writable($uploadDirectory) : (!is_writable($uploadDirectory) && !is_executable($uploadDirectory));

        if ($folderInaccessible) {
            throw new \Exception('Server error. Uploads directory isn\'t writable');
        }
    }

    /**
     * @throws \Exception
     */
    private function checkType()
    {
        $type = $this->request->server->get('HTTP_CONTENT_TYPE', $this->request->server->get('CONTENT_TYPE'));
        if ($type === null) {
            throw new \Exception('No files were uploaded.');
        } elseif (strpos(strtolower($type), 'multipart/') !== 0) {
            throw new \Exception('Server error. Not a multipart request. Please set forceMultipart to default value (true).');
        }
    }

    /**
     * @param string $name
     * @throws \Exception
     */
    private function checkFileExtension(string $name)
    {
        // Validate file extension
        $pathinfo = pathinfo($name);
        $ext = isset($pathinfo['extension']) ? $pathinfo['extension'] : '';

        // Check file extension
        if (!in_array(strtolower($ext), array_map("strtolower", $this->allowedExtensions))) {
            $these = implode(', ', $this->allowedExtensions);
            throw new \Exception('File has an invalid extension, it should be one of ' . $these . '.');
        }
    }

    /**
     * @param UploadedFile $file
     * @throws \Exception
     */
    private function checkFileMimeType(UploadedFile $file)
    {
        // Check file mime type
        if (!in_array(strtolower($file->getMimeType()), array_map("strtolower", $this->allowedMimeTypes))) {
            $these = implode(', ', $this->allowedMimeTypes);
            throw new \Exception('File has an invalid mime type, it should be one of ' . $these . '.');
        }
    }

    /**
     * Deletes all file parts in the chunks folder for files uploaded
     * more than chunksExpireIn seconds ago
     */
    private function cleanupChunksIfNecessary()
    {
        if (!is_writable($this->chunksFolder) || 1 !== mt_rand(1, 1 / $this->chunksCleanupProbability)) {
            return;
        }

        foreach (scandir($this->chunksFolder) as $item) {
            if ($item == "." || $item == "..") {
                continue;
            }

            $path = $this->chunksFolder . DIRECTORY_SEPARATOR . $item;

            if (!is_dir($path)) {
                continue;
            }

            if (time() - filemtime($path) > $this->chunksExpireIn) {
                $this->fileManager->deleteFolder($path);
            }
        }
    }

    /**
     * @return UploadedFile
     * @throws \Exception
     */
    private function getFile(): UploadedFile
    {
        /** @var UploadedFile|null $file */
        $file = $this->request->files->get($this->inputName);

        // check file error
        if (!$file instanceof UploadedFile) {
            throw new \Exception('Upload Error #UNKNOWN');
        }

        return $file;
    }

    /**
     * @param string|null $name
     * @return string
     * @throws \Exception
     */
    private function getRedefinedName(string $name = null): string
    {
        if ($name === null) {
            $name = $this->getName();
        }

        // Validate name
        if ($name === null || $name === '') {
            throw new \Exception('File name empty.');
        }

        return $name;
    }

    /**
     * @param UploadedFile $file
     * @return string
     * @throws \Exception
     */
    private function getSize(UploadedFile $file): string
    {
        $size = $this->request->request->get('qqtotalfilesize', $file->getClientSize());

        // Validate file size
        if ($size == 0) {
            throw new \Exception('File is empty.');
        }

        return $size;
    }

    /**
     * Returns a path to use with this upload. Check that the name does not exist,
     * and appends a suffix otherwise.
     *
     * @param string $uploadDirectory Target directory
     * @param string $filename The name of the file to use.
     * @return false|string
     */
    protected function getUniqueTargetPath($uploadDirectory, $filename)
    {
        // Allow only one process at the time to get a unique file name, otherwise
        // if multiple people would upload a file with the same name at the same time
        // only the latest would be saved.
        if (function_exists('sem_acquire')) {
            $lock = sem_get(ftok(__FILE__, 'u'));
            sem_acquire($lock);
        }

        $pathinfo = pathinfo($filename);
        $base = $pathinfo['filename'];
        $ext = isset($pathinfo['extension']) ? $pathinfo['extension'] : '';
        $ext = $ext == '' ? $ext : '.' . $ext;

        $unique = $base;
        $suffix = 0;

        // Get unique file name for the file, by appending random suffix.
        while ($this->fileManager->exists($uploadDirectory . DIRECTORY_SEPARATOR . $unique . $ext)) {
            $suffix += rand(1, 999);
            $unique = $base . '-' . $suffix;
        }

        $result = $uploadDirectory . DIRECTORY_SEPARATOR . $unique . $ext;

        // Create an empty target file
        if (!touch($result)) {
            // Failed
            $result = false;
        }

        if (function_exists('sem_acquire')) {
            sem_release($lock);
        }

        return $result;
    }

    /**
     * Determines is the OS is Windows or not
     *
     * @return bool
     */
    protected function isWindows(): bool
    {
        return (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
    }

    /**
     * Converts a given size with units to bytes.
     *
     * @param string $str
     * @return int
     */
    protected function toBytes(string $str): int
    {
        $str = trim($str);
        $last = strtolower($str[strlen($str) - 1]);
        $val = (int) substr($str, 0, -1);

        if (is_numeric($last)) {
            $val = (int) $str;
        }

        $last = strtoupper($last);
        if ($last === 'G') {
            return $val * 1073741824;
        }

        if ($last === 'M') {
            return $val * 1048576;
        }

        if ($last === 'K') {
            return $val * 1024;
        }

        return $val;
    }
}
