<?php

namespace Backend\Modules\FormBuilder\Engine;

use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\TwigTemplate as BackendTemplate;

/**
 * Helper class for the form_builder module.
 *
 * Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class Helper
{
    /**
     * Parse a field and return the HTML.
     *
     * @param array $field Field data.
     *
     * @return string
     */
    public static function parseField(array $field): string
    {
        if (!empty($field)) {
            // init
            $form = new BackendForm('tmp', '');
            $tpl = (
                BackendModel::getContainer()->has('template') ?
                BackendModel::getContainer()->get('template') :
                new BackendTemplate()
            );
            $fieldHTML = '';
            $fieldName = 'field' . $field['id'];
            $values = $field['settings']['values'] ?? null;
            $defaultValues = $field['settings']['default_values'] ?? null;
            $placeholder = $field['settings']['placeholder'] ?? null;

            /*
             * Create form and parse to HTML
             */
            // dropdown
            if ($field['type'] === 'dropdown') {
                // values and labels are the same
                $values = array_combine($values, $values);

                // get index of selected item
                $defaultIndex = array_search($defaultValues, $values, true);
                if ($defaultIndex === false) {
                    $defaultIndex = null;
                }

                // create element
                $ddm = $form->addDropdown($fieldName, $values, $defaultIndex);

                // empty default element
                $ddm->setDefaultElement('');

                // get content
                $fieldHTML = $ddm->parse();
            } elseif ($field['type'] === 'datetime') {
                // create element
                if ($field['settings']['input_type'] === 'date') {
                    // calculate default value
                    $amount = $field['settings']['value_amount'];
                    $type = $field['settings']['value_type'];

                    if ($type != '') {
                        switch ($type) {
                            case 'today':
                                $defaultValues = date('d/m/Y');
                                break;
                            case 'day':
                            case 'week':
                            case 'month':
                            case 'year':
                                if ($amount != '') {
                                    $defaultValues = date('d/m/Y', strtotime('+' . $amount . ' ' . $type));
                                }
                                break;
                        }
                    }

                    $datetime = $form->addText($fieldName, $defaultValues);
                } else {
                    $datetime = $form->addTime($fieldName, $defaultValues);
                }
                $datetime->setAttribute('disabled', 'disabled');

                // get content
                $fieldHTML = $datetime->parse();
            } elseif ($field['type'] === 'radiobutton') {
                // make disabled
                foreach ($values as &$value) {
                    $value['attributes']['disabled'] = true;
                }

                // create element
                $rbt = $form->addRadiobutton($fieldName, $values, $defaultValues);

                // get content
                $fieldHTML = $rbt->parse();
            } elseif ($field['type'] === 'checkbox') {
                // rebuild values
                $newValues = [];
                foreach ((array) $values as $value) {
                    $newValues[] = ['label' => $value, 'value' => $value, 'attributes' => ['disabled' => true]];
                }

                // create element
                $chk = $form->addMultiCheckbox($fieldName, $newValues, $defaultValues);

                // get content
                $fieldHTML = $chk->parse();
            } elseif ($field['type'] === 'textbox') {
                // create element
                $txt = $form->addText($fieldName, $defaultValues);
                $txt->setAttribute('disabled', 'disabled');
                $txt->setAttribute('placeholder', $placeholder);

                // get content
                $fieldHTML = $txt->parse();
            } elseif ($field['type'] === 'textarea') {
                // create element
                $txt = $form->addTextarea($fieldName, $defaultValues);
                $txt->setAttribute('cols', 30);
                $txt->setAttribute('disabled', 'disabled');
                $txt->setAttribute('placeholder', $placeholder);

                // get content
                $fieldHTML = $txt->parse();
            } elseif ($field['type'] === 'heading') {
                $fieldHTML = '<h3>' . $values . '</h3>';
            } elseif ($field['type'] === 'paragraph') {
                $fieldHTML = $values;
            } elseif ($field['type'] === 'recaptcha') {
                $fieldHTML = '<p>reCAPTCHA</p>';
            }

            /*
             * Parse the field into the template
             */
            // init
            $tpl->assign('plaintext', false);
            $tpl->assign('simple', false);
            $tpl->assign('multiple', false);
            $tpl->assign('id', $field['id']);
            $tpl->assign('required', isset($field['validations']['required']));

            // plaintext items
            if (in_array($field['type'], ['heading', 'paragraph', 'recaptcha'])) {
                // assign
                $tpl->assign('content', $fieldHTML);
                $tpl->assign('plaintext', true);
            } elseif (in_array($field['type'], ['checkbox', 'radiobutton'])) {
                // name (prefixed by type)
                $name = ($field['type'] === 'checkbox') ?
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

            return $tpl->getContent(BACKEND_MODULES_PATH . '/FormBuilder/Layout/Templates/Field.html.twig');
        }

        // empty field so return empty string
        return '';
    }
}
