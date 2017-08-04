<?php

namespace Common\Core;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Header as BackendHeader;
use Exception;
use Frontend\Core\Header\Header as FrontendHeader;
use Backend\Core\Engine\Url as BackendUrl;
use Frontend\Core\Engine\Url as FrontendUrl;
use SpoonFilter;
use SpoonFormButton;
use SpoonFormDropdown;
use SpoonFormPassword;
use SpoonFormRadiobutton;
use SpoonFormText;
use SpoonFormTextarea;
use SpoonFormTime;

/**
 * This class will initiate the frontend-application
 */
class Form extends \SpoonForm
{
    /**
     * The header instance
     *
     * @var BackendHeader|FrontendHeader
     */
    protected $header;

    /**
     * The URL instance
     *
     * @var BackendUrl|FrontendUrl
     */
    protected $url;

    /**
     * @param string $name Name of the form
     * @param string|null $action The action (URL) whereto the form will be submitted, if not provided it
     *                            will be auto generated.
     * @param string|null $method The method to use when submitting the form, default is POST.
     * @param string $hash The id of the anchor to append to the action-URL.
     * @param bool $useToken Should we automatically add a formtoken?
     */
    public function __construct(
        string $name,
        string $action = null,
        ?string $method = 'post',
        string $hash = null,
        bool $useToken = true
    ) {
        $this->url = Model::getContainer()->get('url');
        $this->header = Model::getContainer()->get('header');

        $action = ($action === null) ? rtrim(Model::getRequest()->getRequestUri(), '/') : (string) $action;

        if ($hash !== null && mb_strlen($hash) > 0) {
            // check if the # is present
            if ($hash[0] !== '#') {
                $hash = '#' . $hash;
            }

            $action .= $hash;
        }

        parent::__construct($name, $action, $method ?? 'post', $useToken);

        $this->setParameter('id', $name);
        $this->setParameter('class', 'fork-form submitWithLink');
    }

    /**
     * Adds a button to the form
     *
     * @param string $name Name of the button.
     * @param string $value The value (or label) that will be printed.
     * @param string $type The type of the button (submit is default).
     * @param string $class Class(es) that will be applied on the button.
     *
     * @throws Exception
     *
     * @return SpoonFormButton
     */
    public function addButton($name, $value, $type = 'submit', $class = null): SpoonFormButton
    {
        $name = (string) $name;
        $value = (string) $value;
        $type = (string) $type;
        $class = ($class !== null) ? (string) $class : 'btn btn-primary';

        // do a check
        if ($type === 'submit' && $name === 'submit') {
            throw new Exception(
                'You can\'t add buttons with the name submit. JS freaks out when we
                replace the buttons with a link and use that link to submit the form.'
            );
        }

        // create and return a button
        return parent::addButton($name, $value, $type, $class);
    }

    /**
     * Adds a date field to the form
     *
     * @param string $name Name of the element.
     * @param mixed $value The value for the element.
     * @param string $type The type (from, till, range) of the datepicker.
     * @param int $date The date to use.
     * @param int $date2 The second date for a rangepicker.
     * @param string $class Class(es) that have to be applied on the element.
     * @param string $classError Class(es) that have to be applied when an error occurs on the element.
     *
     * @throws Exception
     *
     * @return FormDate
     */
    public function addDate(
        $name,
        $value = null,
        $type = null,
        $date = null,
        $date2 = null,
        $class = null,
        $classError = null
    ): FormDate {
        $name = (string) $name;
        $value = ($value !== null) ? (($value !== '') ? (int) $value : '') : null;
        $type = SpoonFilter::getValue($type, ['from', 'till', 'range'], 'none');
        $date = ($date !== null) ? (int) $date : null;
        $date2 = ($date2 !== null) ? (int) $date2 : null;
        $class = ($class !== null) ? (string) $class : 'form-control fork-form-date inputDate';
        $classError = ($classError !== null) ? (string) $classError : 'error form-control-danger';

        // validate
        if ($type === 'from' && ($date === 0 || $date === null)) {
            throw new Exception('A date field with type "from" should have a valid date-parameter.');
        }
        if ($type === 'till' && ($date === 0 || $date === null)) {
            throw new Exception('A date field with type "till" should have a valid date-parameter.');
        }
        if ($type === 'range' && ($date === 0 || $date2 === 0 || $date === null || $date2 === null)) {
            throw new Exception('A date field with type "range" should have 2 valid date-parameters.');
        }

        // set mask and firstday
        $mask = 'd/m/Y';
        $firstDay = 1;

        // build attributes
        $attributes = [];
        $attributes['data-mask'] = str_replace(
            ['d', 'm', 'Y', 'j', 'n'],
            ['dd', 'mm', 'yy', 'd', 'm'],
            $mask
        );
        $attributes['data-firstday'] = $firstDay;
        $attributes['data-year'] = date('Y', $value);
        // -1 because javascript starts at 0
        $attributes['data-month'] = date('n', $value) - 1;
        $attributes['data-day'] = date('j', $value);

        // add extra classes based on type
        switch ($type) {
            case 'from':
                $class .= ' fork-form-date-from inputDatefieldFrom form-control';
                $classError .= ' inputDatefieldFrom';
                $attributes['data-startdate'] = date('Y-m-d', $date);
                break;

            case 'till':
                $class .= ' fork-form-date-till inputDatefieldTill form-control';
                $classError .= ' inputDatefieldTill';
                $attributes['data-enddate'] = date('Y-m-d', $date);
                break;

            case 'range':
                $class .= ' fork-form-date-range inputDatefieldRange form-control';
                $classError .= ' inputDatefieldRange';
                $attributes['data-startdate'] = date('Y-m-d', $date);
                $attributes['data-enddate'] = date('Y-m-d', $date2);
                break;

            default:
                $class .= ' inputDatefieldNormal form-control';
                $classError .= ' inputDatefieldNormal';
                break;
        }

        $this->add(new FormDate($name, $value, $mask, $class, $classError));

        parent::getField($name)->setAttributes($attributes);

        return parent::getField($name);
    }

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
        $classError = ($classError !== null) ? (string) $classError : 'error form-control-danger';

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
     * @return SpoonFormDropdown
     */
    public function addDropdown(
        $name,
        array $values = null,
        $selected = null,
        $multipleSelection = false,
        $class = null,
        $classError = null
    ): SpoonFormDropdown {
        $name = (string) $name;
        $multipleSelection = (bool) $multipleSelection;
        $class = ($class !== null) ? (string) $class : 'form-control fork-form-select';
        $classError = ($classError !== null) ? (string) $classError : 'error form-control-danger';

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
     *
     * @return \SpoonFormMultiCheckbox
     */
    public function addMultiCheckbox($name, array $values, $checked = null, $class = null)
    {
        $name = (string) $name;
        $values = (array) $values;
        $checked = ($checked !== null) ? (array) $checked : null;
        $class = ($class !== null) ? (string) $class : 'fork-form-multi-checkbox';

        // create and return a multi checkbox
        return parent::addMultiCheckbox($name, $values, $checked, $class);
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
     * @return SpoonFormPassword
     */
    public function addPassword(
        $name,
        $value = null,
        $maxLength = null,
        $class = null,
        $classError = null,
        $HTML = false
    ): SpoonFormPassword {
        $name = (string) $name;
        $value = ($value !== null) ? (string) $value : null;
        $maxLength = ($maxLength !== null) ? (int) $maxLength : null;
        $class = ($class !== null) ? (string) $class : 'form-control fork-form-password inputPassword';
        $classError = ($classError !== null) ? (string) $classError : 'error form-control-danger';
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
     *
     * @return SpoonFormRadiobutton
     */
    public function addRadiobutton($name, array $values, $checked = null, $class = null): SpoonFormRadiobutton
    {
        $name = (string) $name;
        $checked = ($checked !== null) ? (string) $checked : null;
        $class = ($class !== null) ? (string) $class : 'fork-form-radio';

        // create and return a radio button
        return parent::addRadiobutton($name, $values, $checked, $class);
    }

    /**
     * Adds a single textfield.
     *
     * @param string $name The name of the element.
     * @param string $value The value inside the element.
     * @param int $maxLength The maximum length for the value.
     * @param string $class Class(es) that will be applied on the element.
     * @param string $classError Class(es) that will be applied on the element when an error occurs.
     * @param bool $HTML Will this element contain HTML?
     *
     * @return SpoonFormText
     */
    public function addText(
        $name,
        $value = null,
        $maxLength = 255,
        $class = null,
        $classError = null,
        $HTML = true
    ): SpoonFormText {
        $name = (string) $name;
        $value = ($value !== null) ? (string) $value : null;
        $maxLength = ($maxLength !== null) ? (int) $maxLength : 255;
        $class = ($class !== null) ? (string) $class : 'form-control fork-form-text';
        $classError = ($classError !== null) ? (string) $classError : 'error form-control-danger';
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
     * @return SpoonFormTextarea
     */
    public function addTextarea(
        $name,
        $value = null,
        $class = null,
        $classError = null,
        $HTML = true
    ): SpoonFormTextarea {
        $name = (string) $name;
        $value = ($value !== null) ? (string) $value : null;
        $class = ($class !== null) ? (string) $class : 'form-control fork-form-textarea';
        $classError = ($classError !== null) ? (string) $classError : 'error form-control-danger';
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
     * @return SpoonFormTime
     */
    public function addTime($name, $value = null, $class = null, $classError = null): SpoonFormTime
    {
        $name = (string) $name;
        $value = ($value !== null) ? (string) $value : null;
        $class = ($class !== null) ? (string) $class : 'form-control fork-form-time inputTime';
        $classError = ($classError !== null) ? (string) $classError : 'error form-control-danger';

        // create and return a time field
        return parent::addTime($name, $value, $class, $classError);
    }

    /**
     * @return string|null
     */
    public static function getUploadMaxFileSize(): ?string
    {
        $uploadMaxFileSize = ini_get('upload_max_filesize');
        if ($uploadMaxFileSize === false) {
            return null;
        }

        // reformat if defined as an integer
        if (is_numeric($uploadMaxFileSize)) {
            return $uploadMaxFileSize / 1024 . 'MB';
        }

        // reformat if specified in kB
        if (mb_strtoupper(mb_substr($uploadMaxFileSize, -1, 1)) === 'K') {
            return mb_substr($uploadMaxFileSize, 0, -1) . 'kB';
        }

        // reformat if specified in MB
        if (mb_strtoupper(mb_substr($uploadMaxFileSize, -1, 1)) === 'M') {
            return $uploadMaxFileSize . 'B';
        }

        // reformat if specified in GB
        if (mb_strtoupper(mb_substr($uploadMaxFileSize, -1, 1)) === 'G') {
            return $uploadMaxFileSize . 'B';
        }

        return $uploadMaxFileSize;
    }

    protected function sessionHasFormToken(): bool
    {
        return Model::getSession()->has('form_token');
    }

    protected function saveTokenToSession($token): void
    {
        Model::getSession()->set('form_token', $token);
    }

    protected function getSessionId(): string
    {
        return Model::getSession()->getId();
    }

    protected function getTokenFromSession(): ?string
    {
        return Model::getSession()->get('form_token');
    }
}
