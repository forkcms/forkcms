<?php

namespace Backend\Modules\FormBuilder\Actions;

use Backend\Core\Engine\Base\ActionAdd as BackendBaseActionAdd;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Language\Language as BL;
use Backend\Modules\FormBuilder\Engine\Model as BackendFormBuilderModel;

/**
 * This is the add-action, it will display a form to create a new item.
 */
class AddEmail extends BackendBaseActionAdd
{
    /**
     * The available templates
     *
     * @var array
     */
    private $templates = [];

    public function execute(): void
    {
        $this->id = $this->getRequest()->query->getInt('id');

        // does the item exist
        if ($this->id !== 0 && BackendFormBuilderModel::exists($this->id)) {
            parent::execute();
            $this->getData();
            $this->templates = BackendFormBuilderModel::getTemplates();
            $this->loadForm();
            $this->validateForm();
            $this->parse();
            $this->display();
        } else {
            // no item found, throw an exceptions, because somebody is fucking with our url
            $this->redirect(BackendModel::createUrlForAction('Index') . '&error=non-existing');
        }
    }

    private function getData(): void
    {
        $this->record = BackendFormBuilderModel::get($this->id);
    }

    private function loadForm(): void
    {
        // set hidden values
        $rbtRecipientOptions = [
            ['label' => BL::lbl('Email'), 'value' => 'email'],
            ['label' => BL::lbl('ValueOfAField'), 'value' => 'field'],
        ];

        // set field values
        $ddnFormFieldsOptions = BackendFormBuilderModel::getRecipientFieldsForDropdown($this->id);

        $this->form = new BackendForm('add');
        $mailerFrom = $this->get('fork.settings')->get('Core', 'mailer_from');
        $this->form->addText('from_name', (isset($mailerFrom['name'])) ? $mailerFrom['name'] : '');
        $this->form
            ->addText('from_email', (isset($mailerFrom['email'])) ? $mailerFrom['email'] : '')
            ->setAttribute('type', 'email')
        ;
        $this->form->addRadiobutton('recipient', $rbtRecipientOptions, 'email');
        $this->form->addText('email_addresses');
        $this->form->addDropdown('email_fields', $ddnFormFieldsOptions)->setDefaultElement('');
        $this->form->addCheckbox('email_data', 0);
        $this->form->addText('email_subject');
        $this->form->addEditor('email_body');

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
            $chkEmailData = $this->form->getField('email_data');
            $txtEmailSubject = $this->form->getField('email_subject');
            $rbtRecipient = $this->form->getField('recipient');
            $txtEmailToAddresses = $this->form->getField('email_addresses');
            $ddnEmailToField = $this->form->getField('email_fields');
            $txtFromName = $this->form->getField('from_name');
            $txtFromEmail = $this->form->getField('from_email');
            $txtEmailBody = $this->form->getField('email_body');

            $emailAddresses = (array) explode(',', $txtEmailToAddresses->getValue());

            // validate fields
            $txtEmailSubject->isFilled(BL::err('FieldIsRequired'));
            $rbtRecipient->isFilled(BL::err('FieldIsRequired'));
            $txtFromName->isFilled(BL::err('FieldIsRequired'));
            $txtFromEmail->isFilled(BL::err('FieldIsRequired'));
            $txtEmailBody->isFilled(BL::err('FieldIsRequired'));

            if ($rbtRecipient->getValue() == 'email') {
                $txtEmailToAddresses->isFilled(BL::err('FieldIsRequireed'));

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
                    $txtEmailToAddresses->addError(BL::getError('EmailIsInvalid'));
                }
            }
            if ($rbtRecipient->getValue() == 'field') {
                $ddnEmailToField->isFilled(BL::err('FieldIsRequireed'));
            }

            // save
            if ($this->form->isCorrect()) {
                // build array
                $values = [];
                $values['language'] = BL::getWorkingLanguage();
                $values['form_id'] = $this->id;
                $values['email_data'] = (int) $chkEmailData->isChecked();
                $values['email_subject'] = $txtEmailSubject->getValue();
                $values['email_recipient'] = $rbtRecipient->getValue();
                $values['email_to_field'] = $ddnEmailToField->getValue();
                $values['email_to_addresses'] = serialize($emailAddresses);
                $values['email_from'] = serialize(
                    [
                        'name' => $txtFromName->getValue(),
                        'email' => $txtFromEmail->getValue(),
                    ]
                );
                $values['email_body'] = $txtEmailBody->getValue();
                $values['email_template'] = count($this->templates) > 1
                    ? $this->form->getField('template')->getValue() : $this->templates[0];
                $values['created_on'] = BackendModel::getUTCDate();
                $values['edited_on'] = BackendModel::getUTCDate();

                // insert the item
                BackendFormBuilderModel::insertEmail($values);

                // redirect
                $this->redirect(
                    BackendModel::createUrlForAction('Emails') . '&id=' . $this->id .
                    '&report=added-email'
                );
            }
        }
    }

    protected function parse(): void
    {
        parent::parse();

        $this->template->assign('item', $this->record);

        // add form name to the breadcrumb
        $this->header->appendDetailToBreadcrumbs($this->record['name']);
    }
}
