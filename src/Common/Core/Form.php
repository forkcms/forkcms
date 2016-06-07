<?php

namespace Common\Core;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Header;
use Backend\Core\Engine\Url;

/**
 * This class will initiate the frontend-application
 *
 * @author Ghazi Triki <ghazi.triki@inhanx.com>
 */
class Form extends \SpoonForm
{
    /**
     * The header instance
     *
     * @var    Header
     */
    protected $header;

    /**
     * The URL instance
     *
     * @var    Url
     */
    protected $URL;

    /**
     * Adds a single checkbox.
     *
     * @param string $name       The name of the element.
     * @param bool   $checked    Should the checkbox be checked?
     * @param string $class      Class(es) that will be applied on the element.
     * @param string $classError Class(es) that will be applied on the element when an error occurs.
     *
     * @return CommonFormCheckbox
     */
    public function addCheckbox($name, $checked = false, $class = null, $classError = null)
    {
        $name = (string) $name;
        $checked = (bool) $checked;
        $class = ($class !== null) ? (string) $class : 'fork-form-checkbox';
        $classError = ($classError !== null) ? (string) $classError : 'error';

        // create and return a checkbox
        $this->add(new CommonFormCheckbox($name, $checked, $class, $classError));

        // return element
        return $this->getField($name);
    }

    /**
     * Adds a single dropdown.
     *
     * @param string $name              Name of the element.
     * @param array  $values            Values for the dropdown.
     * @param string $selected          The selected elements.
     * @param bool   $multipleSelection Is it possible to select multiple items?
     * @param string $class             Class(es) that will be applied on the element.
     * @param string $classError        Class(es) that will be applied on the element when an error occurs.
     *
     * @return \SpoonFormDropdown
     */
    public function addDropdown(
        $name,
        array $values = null,
        $selected = null,
        $multipleSelection = false,
        $class = null,
        $classError = null
    ) {
        $name = (string) $name;
        $values = (array) $values;
        $selected = ($selected !== null) ? $selected : null;
        $multipleSelection = (bool) $multipleSelection;
        $class = ($class !== null) ? (string) $class : 'form-control fork-form-select';
        $classError = ($classError !== null) ? (string) $classError : 'error';

        // special classes for multiple
        if ($multipleSelection) {
            $class .= ' fork-form-select-multiple';
        }

        // create and return a dropdown
        return parent::addDropdown($name, $values, $selected, $multipleSelection, $class, $classError);
    }

    /**
     * Adds a multiple checkbox.
     *
     * @param string $name       The name of the element.
     * @param array  $values     The values for the checkboxes.
     * @param mixed  $checked    Should the checkboxes be checked?
     * @param string $class      Class(es) that will be applied on the element.
     * @param string $classError Class(es) that will be applied on the element when an error occurs.
     *
     * @return \SpoonFormMultiCheckbox
     */
    public function addMultiCheckbox($name, array $values, $checked = null, $class = null, $classError = null)
    {
        $name = (string) $name;
        $values = (array) $values;
        $checked = ($checked !== null) ? (array) $checked : null;
        $class = ($class !== null) ? (string) $class : 'fork-form-multi-checkbox';
        $classError = ($classError !== null) ? (string) $classError : 'error';

        // create and return a multi checkbox
        return parent::addMultiCheckbox($name, $values, $checked, $class, $classError);
    }

    /**
     * Adds a single password field.
     *
     * @param string $name       The name of the field.
     * @param string $value      The value for the field.
     * @param int    $maxLength  The maximum length for the field.
     * @param string $class      Class(es) that will be applied on the element.
     * @param string $classError Class(es) that will be applied on the element when an error occurs.
     * @param bool   $HTML       Will the field contain HTML?
     *
     * @return \SpoonFormPassword
     */
    public function addPassword(
        $name,
        $value = null,
        $maxLength = null,
        $class = null,
        $classError = null,
        $HTML = false
    ) {
        $name = (string) $name;
        $value = ($value !== null) ? (string) $value : null;
        $maxLength = ($maxLength !== null) ? (int) $maxLength : null;
        $class = ($class !== null) ? (string) $class : 'form-control fork-form-password inputPassword';
        $classError = ($classError !== null) ? (string) $classError : 'error';
        $HTML = (bool) $HTML;

        // create and return a password field
        return parent::addPassword($name, $value, $maxLength, $class, $classError, $HTML);
    }

    /**
     * Adds a single radio button.
     *
     * @param string $name       The name of the element.
     * @param array  $values     The possible values for the radio button.
     * @param string $checked    Should the element be checked?
     * @param string $class      Class(es) that will be applied on the element.
     * @param string $classError Class(es) that will be applied on the element when an error occurs.
     *
     * @return \SpoonFormRadiobutton
     */
    public function addRadiobutton($name, array $values, $checked = null, $class = null, $classError = null)
    {
        $name = (string) $name;
        $values = (array) $values;
        $checked = ($checked !== null) ? (string) $checked : null;
        $class = ($class !== null) ? (string) $class : 'fork-form-radio';
        $classError = ($classError !== null) ? (string) $classError : 'error';

        // create and return a radio button
        return parent::addRadiobutton($name, $values, $checked, $class, $classError);
    }

    /**
     * Adds a single textfield.
     *
     * @param string $name       The name of the element.
     * @param string $value      The value inside the element.
     * @param int    $maxLength  The maximum length for the value.
     * @param string $class      Class(es) that will be applied on the element.
     * @param string $classError Class(es) that will be applied on the element when an error occurs.
     * @param bool   $HTML       Will this element contain HTML?
     *
     * @return \SpoonFormText
     */
    public function addText($name, $value = null, $maxLength = 255, $class = null, $classError = null, $HTML = false)
    {
        $name = (string) $name;
        $value = ($value !== null) ? (string) $value : null;
        $maxLength = ($maxLength !== null) ? (int) $maxLength : null;
        $class = ($class !== null) ? (string) $class : 'form-control fork-form-text';
        $classError = ($classError !== null) ? (string) $classError : 'error';
        $HTML = (bool) $HTML;

        // create and return a textfield
        return parent::addText($name, $value, $maxLength, $class, $classError, $HTML);
    }

    /**
     * Adds a single textarea.
     *
     * @param string $name       The name of the element.
     * @param string $value      The value inside the element.
     * @param string $class      Class(es) that will be applied on the element.
     * @param string $classError Class(es) that will be applied on the element when an error occurs.
     * @param bool   $HTML       Will the element contain HTML?
     *
     * @return \SpoonFormTextarea
     */
    public function addTextarea($name, $value = null, $class = null, $classError = null, $HTML = false)
    {
        $name = (string) $name;
        $value = ($value !== null) ? (string) $value : null;
        $class = ($class !== null) ? (string) $class : 'form-control fork-form-textarea';
        $classError = ($classError !== null) ? (string) $classError : 'error';
        $HTML = (bool) $HTML;

        // create and return a textarea
        return parent::addTextarea($name, $value, $class, $classError, $HTML);
    }

    /**
     * Adds a single time field.
     *
     * @param string $name       The name of the element.
     * @param string $value      The value inside the element.
     * @param string $class      Class(es) that will be applied on the element.
     * @param string $classError Class(es) that will be applied on the element when an error occurs.
     *
     * @return \SpoonFormTime
     */
    public function addTime($name, $value = null, $class = null, $classError = null)
    {
        $name = (string) $name;
        $value = ($value !== null) ? (string) $value : null;
        $class = ($class !== null) ? (string) $class : 'form-control fork-form-time inputTime';
        $classError = ($classError !== null) ? (string) $classError : 'error';

        // create and return a time field
        return parent::addTime($name, $value, $class, $classError);
    }
}

/**
 * This is our extended version of \SpoonFormCheckbox
 *
 * @author Jelmer Prins <jelmer@sumocoders.be>
 */
class CommonFormCheckbox extends \SpoonFormCheckbox
{
    /**
     * Returns the value corresponding with the state of the checkbox
     *
     * @param mixed $checked the return value when checked
     * @param mixed $notChecked the return value when not checked
     *
     * @return string
     */
    public function getActualValue($checked = 'Y', $notChecked = 'N')
    {
        return $this->isChecked() ? $checked : $notChecked;
    }
}
