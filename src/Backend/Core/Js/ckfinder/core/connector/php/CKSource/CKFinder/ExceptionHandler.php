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

namespace CKSource\CKFinder;

use CKSource\CKFinder\Exception\CKFinderException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Debug;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use CKSource\CKFinder\Response\JsonResponse;
use \Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * The exception handler class.
 * 
 * All errors are resolved here and passed to the response.
 * 
 * @copyright 2016 CKSource - Frederico Knabben
 */
class ExceptionHandler implements EventSubscriberInterface
{
    /**
     * Flag used to enable the debug mode.
     *
     * @var bool $debug
     */
    protected $debug;

    /**
     * LoggerInterface
     *
     * @var LoggerInterface $logger
     */
    protected $logger;

    protected $translator;

    /**
     * Constructor.
     *
     * @param Translator      $translator translator object
     * @param bool            $debug      `true` if debug mode is enabled
     * @param LoggerInterface $logger     logger
     */
    public function __construct(Translator $translator, $debug = false, LoggerInterface $logger = null)
    {
        $this->translator = $translator;
        $this->debug = $debug;
        $this->logger = $logger;

        if ($debug) {
            set_error_handler(array($this, 'errorHandler'));
        }
    }

    public function onCKFinderError(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        $exceptionCode = $exception->getCode() ?: Error::UNKNOWN;

        $replacements = array();

        $httpStatusCode = 200;

        if ($exception instanceof CKFinderException) {
            $replacements = $exception->getParameters();
            $httpStatusCode = $exception->getHttpStatusCode();
        }

        $message =
            $exceptionCode === Error::CUSTOM_ERROR
                ? $exception->getMessage()
                : $this->translator->translateErrorMessage($exceptionCode, $replacements);

        $response = JsonResponse::create()->withError($exceptionCode, $message);

        $event->setException(new HttpException($httpStatusCode));

        $event->setResponse($response);

        if ($this->debug && $this->logger) {
            $this->logger->error($exception);
        }

        if (filter_var(ini_get('display_errors'), FILTER_VALIDATE_BOOLEAN)) {
            throw $exception;
        }
    }

    /**
     * Custom error handler to catch all errors in the debug mode.
     *
     * @param int    $errno
     * @param string $errstr
     * @param string $errfile
     * @param int    $errline
     *
     * @throws \Exception
     */
    public function errorHandler($errno, $errstr, $errfile, $errline)
    {
        $wrapperException = new \ErrorException($errstr, 0, $errno, $errfile, $errline);
        $this->logger->warning($wrapperException);

        if (filter_var(ini_get('display_errors'), FILTER_VALIDATE_BOOLEAN)) {
            throw $wrapperException;
        }
    }

    /**
     * Returns all events and callbacks.
     * 
     * @see <a href="http://api.symfony.com/2.5/Symfony/Component/EventDispatcher/EventSubscriberInterface.html">EventSubscriberInterface</a>
     * 
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(KernelEvents::EXCEPTION => array('onCKFinderError', -255));
    }
}
