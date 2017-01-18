<?php

namespace Backend\Modules\Locale\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionAdd as BackendBaseActionAdd;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Locale\Engine\Model as BackendLocaleModel;

/**
 * This is the import action, it will display a form to import a XML locale file.
 */
class Import extends BackendBaseActionAdd
{
    /**
     * @var array
     */
    private $filter;

    /**
     * @var string
     */
    private $filterQuery;

    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();
        $this->setFilter();
        $this->loadForm();
        $this->validateForm();
        $this->parse();
        $this->display();
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        $this->frm = new BackendForm('import');
        $this->frm->addFile('file');
        $this->frm->addCheckbox('overwrite');
    }

    /**
     * Sets the filter based on the $_GET array.
     */
    private function setFilter()
    {
        // get filter values
        $this->filter['language'] = ($this->getParameter('language', 'array') != '') ? $this->getParameter('language', 'array') : BL::getWorkingLanguage();
        $this->filter['application'] = $this->getParameter('application');
        $this->filter['module'] = $this->getParameter('module');
        $this->filter['type'] = $this->getParameter('type', 'array');
        $this->filter['name'] = $this->getParameter('name');
        $this->filter['value'] = $this->getParameter('value');

        // build query for filter
        $this->filterQuery = BackendLocaleModel::buildURLQueryByFilter($this->filter);
    }

    /**
     * Validate the form
     */
    private function validateForm()
    {
        if ($this->frm->isSubmitted()) {
            $this->frm->cleanupFields();

            // redefine fields
            /** @var $fileFile \SpoonFormFile */
            $fileFile = $this->frm->getField('file');
            $chkOverwrite = $this->frm->getField('overwrite');

            // name checks
            if ($fileFile->isFilled(BL::err('FieldIsRequired'))) {
                // only xml files allowed
                if ($fileFile->isAllowedExtension(array('xml'), sprintf(BL::getError('ExtensionNotAllowed'), 'xml'))) {
                    // load xml
                    $xml = @simplexml_load_file($fileFile->getTempFileName());

                    // invalid xml
                    if ($xml === false) {
                        $fileFile->addError(BL::getError('InvalidXML'));
                    }
                }
            }

            if ($this->frm->isCorrect()) {
                // import
                $statistics = BackendLocaleModel::importXML($xml, $chkOverwrite->getValue());

                // everything is imported, so redirect to the overview
                $this->redirect(BackendModel::createURLForAction('Index') . '&report=imported&var=' . ($statistics['imported'] . '/' . $statistics['total']) . $this->filterQuery);
            }
        }
    }
}
