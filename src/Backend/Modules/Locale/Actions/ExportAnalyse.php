<?php

namespace Backend\Modules\Locale\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Locale\Engine\AnalyseModel as BackendLocaleModel;
use Symfony\Component\HttpFoundation\Response;

/**
 * This is the export-action, it will create a XML with missing locale items.
 */
class ExportAnalyse extends BackendBaseActionIndex
{
    /**
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
        $this->filter['language'] = $this->getRequest()->query->has('language')
            ? $this->getRequest()->query->get('language')
            : BL::getWorkingLanguage();
    }

    /**
     * Build items array and group all items by application, module, type and name.
     */
    private function setItems(): void
    {
        $this->locale = [];

        // get items
        $frontend = BackendLocaleModel::getNonExistingFrontendLocale($this->filter['language']);

        // group by application, module, type and name
        foreach ($frontend as $item) {
            $item['value'] = null;

            $this->locale[$item['application']][$item['module']][$item['type']][$item['name']][] = $item;
        }

        // no need to keep this around
        unset($frontend);

        // get items
        $backend = BackendLocaleModel::getNonExistingBackendLocale($this->filter['language']);

        // group by application, module, type and name
        foreach ($backend as $item) {
            $item['value'] = null;

            $this->locale[$item['application']][$item['module']][$item['type']][$item['name']][] = $item;
        }

        // no need to keep this around
        unset($backend);
    }
}
