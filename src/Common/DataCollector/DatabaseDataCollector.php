<?php

namespace Common\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DatabaseDataCollector extends DataCollector
{
    private $database;

    public function __construct(\SpoonDatabase $database)
    {
        $this->database = $database;
    }

    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data = array(
            'queries'    => $this->database->getQueries(),
            'queryCount' => count($this->database->getQueries()),
        );
    }

    public function getQueryCount()
    {
        return $this->data['queryCount'];
    }

    public function getQueries()
    {
        return $this->data['queries'];
    }

    public function getName()
    {
        return 'database';
    }
}
