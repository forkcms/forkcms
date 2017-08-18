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

namespace CKSource\CKFinder\Response;

use Symfony\Component\HttpFoundation;

/**
 * The CKFinder JSON response class.
 *
 * @copyright 2016 CKSource - Frederico Knabben
 */
class JsonResponse extends HttpFoundation\JsonResponse
{
    protected $rawData;

    public function __construct($data = null, $status = 200, $headers = array())
    {
        if (null === $data) {
            $data = new \stdClass();
        }

        parent::__construct($data, $status, $headers);

        $this->rawData = $data;
    }

    public function getData()
    {
        return $this->rawData;
    }

    public function setData($data = array())
    {
        $this->rawData = $data;

        return parent::setData($this->rawData);
    }

    public function withError($errorNumber, $errorMessage = null)
    {
        $errorData = array('number' => $errorNumber);

        if ($errorMessage) {
            $errorData['message'] = $errorMessage;
        }

        $data = (array) $this->rawData;

        $data = array('error' => $errorData) + $data;

        $this->setData($data);

        return $this;
    }
}
