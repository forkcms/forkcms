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

    public function execute(): void
    {
        parent::execute();
        $this->setFilter();
        $this->loadForm();
        $this->validateForm();
        $this->parse();
        $this->display();
    }

    private function loadForm(): void
    {
        $this->form = new BackendForm('import');
        $this->form->addFile('file');
        $this->form->addCheckbox('overwrite');
    }

    /**
     * Sets the filter based on the $_GET array.
     */
    private function setFilter(): void
    {
        // get filter values
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

        // build query for filter
        $this->filterQuery = BackendLocaleModel::buildUrlQueryByFilter($this->filter);
    }

    private function validateForm(): void
    {
        if ($this->form->isSubmitted()) {
            $this->form->cleanupFields();

            // redefine fields
            /** @var $fileFile \SpoonFormFile */
            $fileFile = $this->form->getField('file');
            $chkOverwrite = $this->form->getField('overwrite');

            // name checks
            if ($fileFile->isFilled(BL::err('FieldIsRequired'))) {
                // only xml files allowed
                if ($fileFile->isAllowedExtension(['xml'], sprintf(BL::getError('ExtensionNotAllowed'), 'xml'))) {
                    // load xml
                    $xml = @simplexml_load_file($fileFile->getTempFileName());

                    // invalid xml
                    if ($xml === false) {
                        $fileFile->addError(BL::getError('InvalidXML'));
                    }
                }
            }

            if ($this->form->isCorrect()) {
                // import
                $statistics = BackendLocaleModel::importXML($xml, $chkOverwrite->getValue());

                // everything is imported, so redirect to the overview
                $this->redirect(BackendModel::createUrlForAction('Index') . '&report=imported&var=' . ($statistics['imported'] . '/' . $statistics['total']) . $this->filterQuery);
            }
        }
    }
}
