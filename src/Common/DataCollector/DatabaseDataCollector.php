<?php

namespace Common\DataCollector;

use SpoonDatabase;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DatabaseDataCollector extends DataCollector
{
    /**
     * @var SpoonDatabase
     */
    private $database;

    /**
     * DatabaseDataCollector constructor.
     *
     * @param SpoonDatabase $database
     */
    public function __construct(SpoonDatabase $database)
    {
        $this->database = $database;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data = [
            'queries' => array_map(
                function (array $query) {
                    $query['query_formatted'] = \SqlFormatter::format($query['query']);

                    return $query;
                },
                (array) $this->database->getQueries()
            ),
            'queryCount' => count($this->database->getQueries()),
        ];
    }

    /**
     * @return int
     */
    public function getQueryCount(): int
    {
        return $this->data['queryCount'];
    }

    /**
     * @return array[]
     */
    public function getQueries(): array
    {
        return $this->data['queries'];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'database';
    }
}
