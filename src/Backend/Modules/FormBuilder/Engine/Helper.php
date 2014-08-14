<?php

namespace Backend\Modules\FormBuilder\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Template as BackendTemplate;

/**
 * Helper class for the form_builder module.
 *
 * @todo this class should be in helper.php like the other modules do
 *
 * Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class Helper
{
    /**
     * Parse a field and return the HTML.
     *
     * @param array $field Field data.
     * @return string
     */
    public static function parseField(array $field)
    {
        if (!empty($field)) {
            // init
            $frm = new BackendForm('tmp', '');
            $tpl = (BackendModel::getContainer()->has('template') ?
                BackendModel::getContainer()->get('template') :
                new BackendTemplate()
            );
            $fieldHTML = '';
            $fieldName = 'field' . $field['id'];
            $values = (isset($field['settings']['values']) ? $field['settings']['values'] : null);
            $defaultValues = (isset($field['settings']['default_values']) ?
                $field['settings']['default_values'] :
                null
            );

            /**
             * Create form and parse to HTML
             */
            // dropdown
            if ($field['type'] == 'dropdown') {
                // values and labels are the same
                $values = array_combine($values, $values);

                // get index of selected item
                $defaultIndex = array_search($defaultValues, $values, true);
                if ($defaultIndex === false) {
                    $defaultIndex = null;
                }

                // create element
                $ddm = $frm->addDropdown($fieldName, $values, $defaultIndex);

                // empty default element
                $ddm->setDefaultElement('');

                // get content
                $fieldHTML = $ddm->parse();
            } elseif ($field['type'] == 'radiobutton') {
                // rebuild values
                foreach ($values as $value) {
                    $newValues[] = array('label' => $value, 'value' => $value);
                }

                // create element
                $rbt = $frm->addRadiobutton($fieldName, $newValues, $defaultValues);

                // get content
                $fieldHTML = $rbt->parse();
            } elseif ($field['type'] == 'checkbox') {
                // rebuild values
                foreach ($values as $value) {
                    $newValues[] = array('label' => $value, 'value' => $value);
                }

                // create element
                $chk = $frm->addMultiCheckbox($fieldName, $newValues, $defaultValues);

                // get content
                $fieldHTML = $chk->parse();
            } elseif ($field['type'] == 'textbox') {
                // create element
                $txt = $frm->addText($fieldName, $defaultValues);
                $txt->setAttribute('disabled', 'disabled');

                // get content
                $fieldHTML = $txt->parse();
            } elseif ($field['type'] == 'textarea') {
                // create element
                $txt = $frm->addTextarea($fieldName, $defaultValues);
                $txt->setAttribute('cols', 30);
                $txt->setAttribute('disabled', 'disabled');

                // get content
                $fieldHTML = $txt->parse();
            } elseif ($field['type'] == 'heading') {
                $fieldHTML = '<h3>' . $values . '</h3>';
            } elseif ($field['type'] == 'paragraph') {
                $fieldHTML = $values;
            }

            /**
             * Parse the field into the template
             */
            // init
            $tpl->assign('plaintext', false);
            $tpl->assign('simple', false);
            $tpl->assign('multiple', false);
            $tpl->assign('id', $field['id']);
            $tpl->assign('required', isset($field['validations']['required']));

            // plaintext items
            if ($field['type'] == 'heading' || $field['type'] == 'paragraph') {
                // assign
                $tpl->assign('content', $fieldHTML);
                $tpl->assign('plaintext', true);
            } elseif ($field['type'] == 'checkbox' || $field['type'] == 'radiobutton') {
                // name (prefixed by type)
                $name = ($field['type'] == 'checkbox') ?
                    'chk' . \SpoonFilter::ucfirst($fieldName) :
                    'rbt' . \SpoonFilter::ucfirst($fieldName)
                ;

                // rebuild so the html is stored in a general name (and not rbtName)
                foreach ($fieldHTML as &$item) {
                    $item['field'] = $item[$name];
                }

                // show multiple
                $tpl->assign('label', $field['settings']['label']);
                $tpl->assign('items', $fieldHTML);
                $tpl->assign('multiple', true);
            } else {
                // assign
                $tpl->assign('label', $field['settings']['label']);
                $tpl->assign('field', $fieldHTML);
                $tpl->assign('simple', true);
            }

            return $tpl->getContent(BACKEND_MODULE_PATH . '/Layout/Templates/Field.tpl');
        } else {
            // empty field so return empty string
            return '';
        }
    }
}
