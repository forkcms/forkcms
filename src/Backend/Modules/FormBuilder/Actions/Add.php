<?php

namespace Backend\Modules\FormBuilder\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionAdd as BackendBaseActionAdd;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Language\Language as BL;
use Frontend\Core\Language\Language as FL;
use Backend\Modules\FormBuilder\Engine\Model as BackendFormBuilderModel;

/**
 * This is the add-action, it will display a form to create a new item.
 */
class Add extends BackendBaseActionAdd
{
    /**
     * The available templates
     *
     * @var array
     */
    private $templates = [];

    public function execute(): void
    {
        parent::execute();
        $this->templates = BackendFormBuilderModel::getTemplates();
        $this->loadForm();
        $this->validateForm();
        $this->parse();
        $this->display();
    }

    private function loadForm(): void
    {
        $this->form = new BackendForm('add');
        $this->form->addText('name');
        $this->form->addDropdown(
            'method',
            [
                'database' => BL::getLabel('MethodDatabase'),
                'database_email' => BL::getLabel('MethodDatabaseEmail'),
                'email' => BL::getLabel('MethodEmail'),
            ],
            'database_email'
        );
        $this->form->addText('email');
        $this->form->addText('email_subject');
        $this->form->addText('identifier', BackendFormBuilderModel::createIdentifier());
        $this->form->addEditor('success_message');

        // if we have multiple templates, add a dropdown to select them
        if (count($this->templates) > 1) {
            $this->form->addDropdown('template', array_combine($this->templates, $this->templates));
        }
    }

    private function validateForm(): void
    {
        if ($this->form->isSubmitted()) {
            $this->form->cleanupFields();

            // shorten the fields
            $txtName = $this->form->getField('name');
            $txtEmail = $this->form->getField('email');
            $txtEmailSubject = $this->form->getField('email_subject');
            $ddmMethod = $this->form->getField('method');
            $txtSuccessMessage = $this->form->getField('success_message');
            $txtIdentifier = $this->form->getField('identifier');

            $emailAddresses = (array) explode(',', $txtEmail->getValue());

            // validate fields
            $txtName->isFilled(BL::getError('NameIsRequired'));
            $txtSuccessMessage->isFilled(BL::getError('SuccessMessageIsRequired'));
            if ($ddmMethod->isFilled(BL::getError('NameIsRequired')) && $ddmMethod->getValue() == 'database_email') {
                $error = false;

                // check the addresses
                foreach ($emailAddresses as $address) {
                    $address = trim($address);

                    if (!filter_var($address, FILTER_VALIDATE_EMAIL)) {
                        $error = true;
                        break;
                    }
                }

                // add error
                if ($error) {
                    $txtEmail->addError(BL::getError('EmailIsInvalid'));
                }
            }

            // identifier
            if ($txtIdentifier->isFilled()) {
                // invalid characters
                if (!\SpoonFilter::isValidAgainstRegexp('/^[a-zA-Z0-9\.\_\-]+$/', $txtIdentifier->getValue())) {
                    $txtIdentifier->setError(BL::getError('InvalidIdentifier'));
                } elseif (BackendFormBuilderModel::existsIdentifier($txtIdentifier->getValue())) {
                    // unique identifier
                    $txtIdentifier->setError(BL::getError('UniqueIdentifier'));
                }
            }

            if ($this->form->isCorrect()) {
                // build array
                $values = [];
                $values['language'] = BL::getWorkingLanguage();
                $values['user_id'] = BackendAuthentication::getUser()->getUserId();
                $values['name'] = $txtName->getValue();
                $values['method'] = $ddmMethod->getValue();
                $values['email'] = ($ddmMethod->getValue() === 'database_email' || $ddmMethod->getValue() === 'email')
                    ? serialize($emailAddresses) : null;
                $values['email_subject'] = empty($txtEmailSubject->getValue()) ? null : $txtEmailSubject->getValue();
                $values['email_template'] = count($this->templates) > 1
                    ? $this->form->getField('template')->getValue() : $this->templates[0];
                $values['success_message'] = $txtSuccessMessage->getValue(true);
                $values['identifier'] = ($txtIdentifier->isFilled() ?
                    $txtIdentifier->getValue() :
                    BackendFormBuilderModel::createIdentifier()
                );
                $values['created_on'] = BackendModel::getUTCDate();
                $values['edited_on'] = BackendModel::getUTCDate();

                // insert the item
                $id = BackendFormBuilderModel::insert($values);

                // set frontend locale
                FL::setLocale(BL::getWorkingLanguage(), true);

                // create submit button
                $field = [];
                $field['form_id'] = $id;
                $field['type'] = 'submit';
                $field['settings'] = serialize(['values' => \SpoonFilter::ucfirst(FL::getLabel('Send'))]);
                BackendFormBuilderModel::insertField($field);

                // everything is saved, so redirect to the editform
                $this->redirect(
                    BackendModel::createUrlForAction('Edit') . '&id=' . $id .
                    '&report=added&var=' . rawurlencode($values['name']) . '#tabFields'
                );
            }
        }
    }
}
