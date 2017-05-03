<?php

namespace Backend\Modules\MediaLibrary\Component;

use Backend\Modules\MediaLibrary\Manager\FileManager;
use Exception;
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

    public function __construct(Request $request, FileManager $fileManager)
    {
        $this->request = $request;
        $this->fileManager = $fileManager;
    }

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

    public function getUploadName(): string
    {
        return $this->uploadName;
    }

    public function combineChunks(string $uploadDirectory, string $name = null): array
    {
        $uuid = $this->request->request->get('qquuid');
        if ($name === null) {
            $name = $this->getName();
        }
        $targetFolder = $this->chunksFolder . DIRECTORY_SEPARATOR . $uuid;
        $totalParts = $this->request->request->getInt('qqtotalparts', 1);

        $targetPath = implode(DIRECTORY_SEPARATOR, [$uploadDirectory, $uuid, $name]);
        $this->uploadName = $name;

        if (!$this->fileManager->exists($targetPath)) {
            mkdir(dirname($targetPath), 0777, true);
        }
        $target = fopen($targetPath, 'wb');

        for ($i = 0; $i < $totalParts; ++$i) {
            $chunk = fopen($targetFolder . DIRECTORY_SEPARATOR . $i, 'rb');
            stream_copy_to_stream($chunk, $target);
            fclose($chunk);
        }

        // Success
        fclose($target);

        for ($i = 0; $i < $totalParts; ++$i) {
            unlink($targetFolder . DIRECTORY_SEPARATOR . $i);
        }

        rmdir($targetFolder);

        if ($this->sizeLimit !== null && filesize($targetPath) > $this->sizeLimit) {
            unlink($targetPath);
            http_response_code(413);

            return ['success' => false, 'uuid' => $uuid, 'preventRetry' => true];
        }

        return ['success' => true, 'uuid' => $uuid];
    }

    public function handleUpload(string $uploadDirectory, string $name = null): array
    {
        $this->cleanupChunksIfNecessary();

        try {
            $this->checkMaximumSize();
            $this->checkUploadDirectory($uploadDirectory);
            $this->checkType();
            $file = $this->getFile();
            $size = $this->getSize($file);

            if ($this->sizeLimit !== null && $size > $this->sizeLimit) {
                return ['error' => 'File is too large.', 'preventRetry' => true];
            }

            $name = $this->getRedefinedName($name);
            $this->checkFileExtension($name);
            $this->checkFileMimeType($file);
        } catch (Exception $e) {
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

            return ['success' => $success, 'uuid' => $uuid];
        }

        // Non-chunked upload
        $target = implode(DIRECTORY_SEPARATOR, [$uploadDirectory, $uuid, $name]);

        if ($target) {
            $this->uploadName = basename($target);

            if (!is_dir(dirname($target))) {
                mkdir(dirname($target), 0777, true);
            }
            if (move_uploaded_file($file->getRealPath(), $target)) {
                return ['success' => true, 'uuid' => $uuid];
            }
        }

        return ['error' => 'Could not save uploaded file.' . 'The upload was cancelled, or server error encountered'];
    }

    private function checkMaximumSize(): void
    {
        // Check that the max upload size specified in class configuration does not exceed size allowed by server config
        if ($this->toBytes(ini_get('post_max_size')) < $this->sizeLimit ||
            $this->toBytes(ini_get('upload_max_filesize')) < $this->sizeLimit
        ) {
            $neededRequestSize = max(1, $this->sizeLimit / 1024 / 1024) . 'M';

            throw new Exception(
                'Server error. Increase post_max_size and upload_max_filesize to ' . $neededRequestSize
            );
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
     *
     * @throws Exception
     */
    private function checkUploadDirectory(string $uploadDirectory): void
    {
        if (($this->isWindows() && !is_writable($uploadDirectory))
            || (!is_writable($uploadDirectory) && !is_executable($uploadDirectory))) {
            throw new Exception('Server error. Uploads directory isn\'t writable');
        }
    }

    private function checkType()
    {
        $type = $this->request->server->get('HTTP_CONTENT_TYPE', $this->request->server->get('CONTENT_TYPE'));
        if ($type === null) {
            throw new Exception('No files were uploaded.');
        }

        if (strpos(strtolower($type), 'multipart/') !== 0) {
            throw new Exception(
                'Server error. Not a multipart request. Please set forceMultipart to default value (true).'
            );
        }
    }

    private function checkFileExtension(string $name): void
    {
        // Validate file extension
        $pathinfo = pathinfo($name);
        $ext = $pathinfo['extension'] ?? '';

        // Check file extension
        if (!in_array(strtolower($ext), array_map('strtolower', $this->allowedExtensions), true)) {
            $these = implode(', ', $this->allowedExtensions);
            throw new Exception('File has an invalid extension, it should be one of ' . $these . '.');
        }
    }

    private function checkFileMimeType(UploadedFile $file): void
    {
        // Check file mime type
        if (!in_array(strtolower($file->getMimeType()), array_map('strtolower', $this->allowedMimeTypes), true)) {
            $these = implode(', ', $this->allowedMimeTypes);
            throw new Exception('File has an invalid mime type, it should be one of ' . $these . '.');
        }
    }

    /**
     * Deletes all file parts in the chunks folder for files uploaded
     * more than chunksExpireIn seconds ago
     */
    private function cleanupChunksIfNecessary(): void
    {
        if (!is_writable($this->chunksFolder) || 1 !== random_int(1, 1 / $this->chunksCleanupProbability)) {
            return;
        }

        foreach (scandir($this->chunksFolder) as $item) {
            if ($item === '.' || $item === '..') {
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

    private function getFile(): UploadedFile
    {
        /** @var UploadedFile|null $file */
        $file = $this->request->files->get($this->inputName);

        // check file error
        if (!$file instanceof UploadedFile) {
            throw new Exception('Upload Error #UNKNOWN');
        }

        return $file;
    }

    private function getRedefinedName(string $name = null): string
    {
        if ($name === null) {
            $name = $this->getName();
        }

        // Validate name
        if ($name === null || $name === '') {
            throw new Exception('File name empty.');
        }

        return $name;
    }

    private function getSize(UploadedFile $file): int
    {
        $size = (int) $this->request->request->get('qqtotalfilesize', $file->getClientSize());

        // Validate file size
        if ($size === 0) {
            throw new Exception('File is empty.');
        }

        return $size;
    }

    /**
     * Returns a path to use with this upload. Check that the name does not exist,
     * and appends a suffix otherwise.
     *
     * @param string $uploadDirectory Target directory
     * @param string $filename The name of the file to use.
     *
     * @return false|string
     */
    protected function getUniqueTargetPath(string $uploadDirectory, string $filename)
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
        $ext = $ext === '' ? $ext : '.' . $ext;

        $unique = $base;
        $suffix = 0;

        // Get unique file name for the file, by appending random suffix.
        while ($this->fileManager->exists($uploadDirectory . DIRECTORY_SEPARATOR . $unique . $ext)) {
            $suffix += random_int(1, 999);
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
        return 0 === stripos(PHP_OS, 'WIN');
    }

    /**
     * Converts a given size with units to bytes.
     *
     * @param string $str
     *
     * @return int
     */
    protected function toBytes(string $str): int
    {
        $str = trim($str);
        $unit = strtolower($str[strlen($str) - 1]);
        if (is_numeric($unit)) {
            return (int) $str;
        }

        $val = (int) substr($str, 0, -1);
        switch (strtoupper($unit)) {
            case 'G':
                return $val * 1073741824;
            case 'M':
                return $val * 1048576;
            case 'K':
                return $val * 1024;
            default:
                return $val;
        }
    }
}
