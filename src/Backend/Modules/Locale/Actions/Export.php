<?php

namespace Backend\Modules\Locale\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Locale\Engine\Model as BackendLocaleModel;
use Symfony\Component\HttpFoundation\Response;

/**
 * This is the export-action, it will create a XML with locale items.
 */
class Export extends BackendBaseActionIndex
{
    /**
     * Filter variables.
     *
     * @var array
     */
    private $filter;

    /**
     * Locale items.
     *
     * @var array
     */
    private $locale;

    /**
     * Builds the query for this datagrid.
     *
     * @return array An array with two arguments containing the query and its parameters.
     */
    private function buildQuery(): array
    {
        $parameters = [];

        // start of query
        $query =
            'SELECT l.id, l.language, l.application, l.module, l.type, l.name, l.value
             FROM locale AS l
             WHERE 1';

        // add language
        if ($this->filter['language'] !== null) {
            // create an array for the languages, surrounded by quotes (example: 'en')
            $languages = [];
            foreach ($this->filter['language'] as $key => $val) {
                $languages[$key] = '\'' . $val . '\'';
            }

            $query .= ' AND l.language IN (' . implode(',', $languages) . ')';
        }

        // add application
        if ($this->filter['application'] !== null) {
            $query .= ' AND l.application = ?';
            $parameters[] = $this->filter['application'];
        }

        // add module
        if ($this->filter['module'] !== null) {
            $query .= ' AND l.module = ?';
            $parameters[] = $this->filter['module'];
        }

        // add type
        if ($this->filter['type'] !== null) {
            // create an array for the types, surrounded by quotes (example: 'lbl')
            $types = [];
            foreach ($this->filter['type'] as $key => $val) {
                $types[$key] = '\'' . $val . '\'';
            }

            $query .= ' AND l.type IN (' . implode(',', $types) . ')';
        }

        // add name
        if ($this->filter['name'] !== null) {
            $query .= ' AND l.name LIKE ?';
            $parameters[] = '%' . $this->filter['name'] . '%';
        }

        // add value
        if ($this->filter['value'] !== null) {
            $query .= ' AND l.value LIKE ?';
            $parameters[] = '%' . $this->filter['value'] . '%';
        }

        // filter checkboxes
        if ($this->filter['ids']) {
            // make really sure we are working with integers
            foreach ($this->filter['ids'] as &$id) {
                $id = (int) $id;
            }

            $query .= ' AND l.id IN (' . implode(',', $this->filter['ids']) . ') ';
        }

        // end of query
        $query .= ' ORDER BY l.application, l.module, l.name ASC';

        // cough up
        return [$query, $parameters];
    }

    /**
     * Create the XML based on the locale items.
     *
     * @return Response
     */
    public function getContent(): Response
    {
        $charset = BackendModel::getContainer()->getParameter('kernel.charset');

        // create XML
        $xmlOutput = BackendLocaleModel::createXMLForExport($this->locale);

        return new Response(
            $xmlOutput,
            Response::HTTP_OK,
            [
                'Content-Disposition' => 'attachment; filename="locale_' . BackendModel::getUTCDate('d-m-Y') . '.xml"',
                'Content-Type' => 'application/octet-stream;charset=' . $charset,
                'Content-Length' => '' . mb_strlen($xmlOutput),
            ]
        );
    }

    public function execute(): void
    {
        $this->setFilter();
        $this->setItems();
    }

    /**
     * Sets the filter based on the $_GET array.
     */
    private function setFilter(): void
    {
        $this->filter['language'] = $this->getRequest()->query->get('language', []);
        if (empty($this->filter['language'])) {
            $this->filter['language'] = BL::getWorkingLanguage();
        }
        $this->filter['application'] = $this->getRequest()->query->get('application');
        $this->filter['module'] = $this->getRequest()->query->get('module');
        $this->filter['type'] = $this->getRequest()->query->get('type', '');
        if ($this->filter['type'] === '') {
            $this->filter['type'] = null;
        }
        $this->filter['name'] = $this->getRequest()->query->get('name');
        $this->filter['value'] = $this->getRequest()->query->get('value');

        $ids = $this->getRequest()->query->get('ids', '');
        if ($ids === '') {
            $ids = [];
        } else {
            $ids = explode('|', $ids);
        }
        $this->filter['ids'] = $ids;

        foreach ($this->filter['ids'] as $id) {
            // someone is messing with the url, clear ids
            if (!is_numeric($id)) {
                $this->filter['ids'] = [];
                break;
            }
        }
    }

    /**
     * Build items array and group all items by application, module, type and name.
     */
    private function setItems(): void
    {
        list($query, $parameters) = $this->buildQuery();

        // get locale from the database
        $items = (array) $this->get('database')->getRecords($query, $parameters);

        // init
        $this->locale = [];

        // group by application, module, type and name
        foreach ($items as $item) {
            $this->locale[$item['application']][$item['module']][$item['type']][$item['name']][] = $item;
        }

        // no need to keep this around
        unset($items);
    }
}
