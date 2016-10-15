<?php

namespace Common\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DatabaseDataCollector extends DataCollector
{
    private $database;

    /**
     * DatabaseDataCollector constructor.
     * @param \SpoonDatabase $database
     */
    public function __construct(\SpoonDatabase $database)
    {
        $this->database = $database;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data = array(
            'queries' => $this->database->getQueries(),
            'queryCount' => count($this->database->getQueries()),
        );

        foreach ($this->data['queries'] as &$query) {
            $query['query_formatted'] = \SqlFormatter::format($query['query']);
        }
    }

    /**
     * @return mixed
     */
    public function getQueryCount()
    {
        return $this->data['queryCount'];
    }

    /**
     * @return mixed
     */
    public function getQueries()
    {
        return $this->data['queries'];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'database';
    }
}
