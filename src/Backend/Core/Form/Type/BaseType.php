<?php

namespace Backend\Core\Form\Type;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Exception;

/**
 * This is the base form type, it will allow us to create reusable
 * forms
 *
 * @author Wouter Sioen <wouter@woutersioen.be>
 */
class BaseType
{
    /**
     * @var BackendForm
     */
    protected $form;

    /**
     * @param array[optional] $data
     */
    public function buildForm($data = null)
    {
        throw new Exception(
            'Classes extending the BaseType should contain a buildForm function'
        );
    }

    /**
     * Fetches the data from the form fields
     * 
     * @return array
     */
    public function getData()
    {
        // form fields with these names shouldn't be added to the data array
        $ignoredFields = array('form', '_utf8', 'form_token');

        // build an array with all data
        $data = array();
        foreach ($this->form->getFields() as $field) {
            $name = $field->getAttribute('name');

            if (in_array($name, $ignoredFields)) continue;

            $data[$name] = $field->getValue();
        }

        // if our child class want to add extra data, call the needed function
        if (method_exists($this, 'extendData')) {
            $data = $this->extendData($data);
        }

        return $data;
    }

    /**
     * This parses our form to the template
     * 
     * @param Backend\Core\Engine\Template $tpl
     */
    public function parse($tpl)
    {
        $this->form->parse($tpl);
    }
}
