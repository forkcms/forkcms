<?php

/*
 * CKFinder
 * ========
 * http://cksource.com/ckfinder
 * Copyright (C) 2007-2016, CKSource - Frederico Knabben. All rights reserved.
 *
 * The software, this file and its contents are subject to the CKFinder
 * License. Please read the license.txt file before using, installing, copying,
 * modifying or distribute this file or part of its contents. The contents of
 * this file is part of the Source Code of CKFinder.
 */

namespace CKSource\CKFinder\Operation;

use CKSource\CKFinder\CKFinder;
use CKSource\CKFinder\Filesystem\Path;
use CKSource\CKFinder\Response\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * The OperationManager class.
 *
 * A class used for tracking the progress of the time consuming operations.
 */
class OperationManager
{
    /**
     * Time interval in seconds for operation status updates.
     */
    const UPDATE_STATUS_INTERVAL = 2;

    /**
     * Time interval in seconds for extending the execution time of the script.
     */
    const EXTEND_EXECUTION_INTERVAL = 20;

    /**
     * @var CKFinder
     */
    protected $app;

    /**
     * The CKFinder temporary directory path.
     *
     * @var string
     */
    protected $tempDirectory;

    /**
     * @var string unique identifier of started operation
     */
    protected $startedOperationId;

    /**
     * Start time timestamp.
     *
     * @var int
     */
    protected $startTime;

    /**
     * Last status update timestamp.
     *
     * @var int
     */
    protected $lastUpdateTime;

    /**
     * Last execution time extending timestamp.
     *
     * @var int
     */
    protected $lastExtendExecutionTime;

    /**
     * Constructor.
     *
     * @param CKFinder $app
     */
    public function __construct(CKFinder $app)
    {
        $this->app = $app;
        $this->tempDirectory = $app['config']->get('tempDirectory');
    }

    /**
     * Validates the operation ID.
     *
     * @param string $operationId
     *
     * @return bool `true` if the operation ID format is valid.
     */
    protected function isValidOperationId($operationId)
    {
        return (bool) preg_match('/^[a-z0-9]{16}$/', $operationId);
    }

    /**
     * Starts a time consuming operation in the current request.

     * @return bool `true` if operation tracking was started.
     */
    public function start()
    {
        $request = $this->app->getRequest();
        $operationId = (string) $request->query->get('operationId');

        if (null === $operationId || !$this->isValidOperationId($operationId)) {
            return false;
        }

        if (!mkdir($this->getFilePath($operationId, null))) {
            return false;
        }

        $this->startedOperationId = $operationId;
        $this->startTime = time();

        ignore_user_abort();

        // Session needs to be closed to not block probing requests
        session_write_close();

        return true;
    }

    /**
     * Aborts an operation with a given ID.
     *
     * @param string $operationId
     *
     * @return bool `true` if the operation was aborted.
     */
    public function abort($operationId)
    {
        if (!$this->isValidOperationId($operationId) || !$this->operationStarted($operationId)) {
            return false;
        }

        file_put_contents($this->getFilePath($operationId, 'abort'), serialize(true));

        return true;
    }

    /**
     * Checks if the operation started in the current request was aborted.
     *
     * @return bool `true` if the operation was aborted.
     */
    public function isAborted()
    {
        if (!$this->startedOperationId) {
            return false;
        }

        clearstatcache();

        return $this->operationStarted($this->startedOperationId) &&
               file_exists($this->getFilePath($this->startedOperationId, 'abort'));
    }

    /**
     * Updates the status of the current operation.
     *
     * @param array $status data describing the operation status.
     */
    public function updateStatus(array $status)
    {
        if ($this->startedOperationId) {
            $currentTime = time();

            if ($currentTime - $this->lastUpdateTime >= self::UPDATE_STATUS_INTERVAL) {
                $this->extendExecutionTime($currentTime);

                $this->lastUpdateTime = $currentTime;

                file_put_contents($this->getFilePath($this->startedOperationId), serialize($status));
            }
        }
    }

    /**
     * Extends the execution time of the script.
     *
     * @param int $currentTime current timestamp
     */
    protected function extendExecutionTime($currentTime)
    {
        if ($currentTime - $this->lastExtendExecutionTime >= self::EXTEND_EXECUTION_INTERVAL) {
            set_time_limit(30);

            $this->lastExtendExecutionTime = $currentTime;

            // Emit some whitespaces for Nginx + FPM configuration to avoid 504 Gateway Timeout error
            if (function_exists('fastcgi_finish_request')) {
                // Clear the buffer to remove any garbage before flushing
                Response::closeOutputBuffers(0, false);
                echo ' ';
                @ob_end_flush();
                @flush();
            }
        }
    }

    /**
     * Returns the status of the current operation.
     *
     * @param string $operationId
     *
     * @return array operation status data
     */
    public function getStatus($operationId)
    {
        if ($this->isValidOperationId($operationId)) {
            $filePath = $this->getFilePath($operationId);
            if (file_exists($filePath)) {
                return unserialize(file_get_contents($filePath));
            }
        }

        return null;
    }

    /**
     * Returns a path for a file located in the current operation temporary directory.
     *
     * @param string $operationId
     * @param string $file
     *
     * @return string file path
     */
    protected function getFilePath($operationId, $file = 'status')
    {
        return Path::combine($this->tempDirectory, 'ckf-operation-' . $operationId, $file);
    }

    /**
     * Checks if a temporary directory for an operation with a given ID exists.
     *
     * @param string $operationId
     *
     * @return bool `true` if the directory exists - the operation was started.
     */
    protected function operationStarted($operationId)
    {
        $directoryPath = $this->getFilePath($operationId, null);

        return is_dir($directoryPath);
    }

    /**
     * Adds information about aborting to the long running request response.
     */
    public function addInfoToResponse()
    {
        $this->app->on(KernelEvents::RESPONSE, function (FilterResponseEvent $event) {
            $response = $event->getResponse();

            if ($response instanceof JsonResponse) {
                $responseData = (array) $response->getData();
                $responseData = array('aborted' => $this->isAborted()) + $responseData;
                $response->setData($responseData);
            }
        }, 512);
    }

    /**
     * Destructor to remove temporary files if the operation was started for the current request.
     */
    public function __destruct()
    {
        if ($this->startedOperationId) {
            $directoryPath = $this->getFilePath($this->startedOperationId, null);
            $toRemove = array(
                $statusFilePath = Path::combine($directoryPath, 'status'),
                $abortFilePath = Path::combine($directoryPath, 'abort')
            );

            foreach ($toRemove as $filePath) {
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            if (is_dir($directoryPath)) {
                rmdir($directoryPath);
            }
        }
    }
}
