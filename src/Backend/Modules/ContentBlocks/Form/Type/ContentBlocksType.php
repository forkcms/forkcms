<?php

namespace Backend\Modules\ContentBlocks\Form\Type;

use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Form\Type\BaseType;
use Backend\Modules\ContentBlocks\Engine\Model as BackendContentBlocksModel;

class ContentBlocksType extends BaseType
{
    /**
     * Data for our dropdown
     * 
     * @var array
     */
    private $templates;

    /**
     * @param array[optional] $data
     */
    public function buildForm($data = null)
    {
        $this->templates = BackendContentBlocksModel::getTemplates();

        $this->form = new BackendForm('contentBlocks');
        $this->form->addText(
            'title',
            ($data) ? $data['title'] : null,
            null, 'inputText title', 'inputTextError title'
        );
        $this->form->addEditor(
            'text',
            ($data) ? $data['text'] : null
        );
        $this->form->addCheckbox(
            'hidden',
            ($data) ? $data['hidden'] == 'Y' : true
        );

        // if we have multiple templates, add a dropdown to select them
        if (count($this->templates) > 1) {
            $this->form->addDropdown(
                'template', array_combine($this->templates, $this->templates),
                ($data) ? $data['template'] : null
            );
        }
    }

    /**
     * Check if our form is valid
     * 
     * @return bool
     */
    public function isValid()
    {
        if(!$this->form->isSubmitted()) return false;

        $this->form->cleanupFields();
        $fields = $this->form->getFields();

        // validate fields
        $fields['title']->isFilled(BL::err('TitleIsRequired'));

        if(!$this->form->isCorrect()) return false;

        return true;
    }

    /**
     * Adds needed fiels to the data object
     * 
     * @param array $data
     * @return array $data
     */
    public function extendData($data)
    {
        // add the data if necessary
        if (!array_key_exists('template', $data)) {
            $data['template'] = $this->templates[0];
        }

        $data['language'] = BL::getWorkingLanguage();
        $data['user_id'] = BackendAuthentication::getUser()->getUserId();
        $data['status'] = 'active';
        $data['edited_on'] = $data['created_on'] = BackendModel::getUTCDate();

        // our hidden column is a checkbox. Convert to 'Y' or 'N'
        $data['hidden'] = $data['hidden'] ? 'Y' : 'N';

        return $data;
    }
}
