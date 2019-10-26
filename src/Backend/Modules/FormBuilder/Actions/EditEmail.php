<?php

namespace Backend\Modules\FormBuilder\Actions;

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Language\Language as BL;
use Backend\Form\Type\DeleteType;
use Backend\Modules\FormBuilder\Engine\Autocomplete;
use Frontend\Core\Language\Language as FL;
use Backend\Modules\FormBuilder\Engine\Model as BackendFormBuilderModel;
use Backend\Modules\FormBuilder\Engine\Helper as FormBuilderHelper;

/**
 * This is the edit-action, it will display a form to edit an existing item
 */
class EditEmail extends BackendBaseActionEdit
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
        $this->formId = $this->getRequest()->query->getInt('formId');

        // does the item exist
        if ($this->formId !== 0 && $this->id !== 0 && BackendFormBuilderModel::exists($this->formId) && BackendFormBuilderModel::existsEmail($this->id)) {
            parent::execute();
            $this->getData();
            $this->loadForm();
            $this->validateForm();
            $this->loadDeleteForm();
            $this->parse();
            $this->display();
        } else {
            // no item found, throw an exceptions, because somebody is fucking with our url
            $this->redirect(BackendModel::createUrlForAction('Emails') . '&error=non-existing&id=' . $this->formId);
        }
    }

    private function getData(): void
    {
        $this->record = BackendFormBuilderModel::get($this->formId);
        $this->email = BackendFormBuilderModel::getEmail($this->id);
        $this->templates = BackendFormBuilderModel::getTemplates();
    }

    private function loadForm(): void
    {
        // set hidden values
        $rbtRecipientOptions = [
            ['label' => BL::lbl('Email'), 'value' => 'email'],
            ['label' => BL::lbl('ValueOfAField'), 'value' => 'field'],
        ];

        // set field values
        $ddnFormFieldsOptions = BackendFormBuilderModel::getRecipientFieldsForDropdown($this->record['id']);

        $this->form = new BackendForm('edit');
        $this->form->addText('from_name', $this->email['email_from']['name']);
        $this->form
            ->addText('from_email', $this->email['email_from']['email'])
            ->setAttribute('type', 'email')
        ;
        $this->form->addRadiobutton('recipient', $rbtRecipientOptions, $this->email['email_recipient']);
        $this->form->addText('email_addresses', implode(',', (array) $this->email['email_to_addresses']));
        $this->form->addDropdown('email_fields', $ddnFormFieldsOptions, $this->email['email_to_field'])->setDefaultElement('');
        $this->form->addCheckbox('email_data', $this->email['email_data']);
        $this->form->addText('email_subject', $this->email['email_subject']);
        $this->form->addEditor('email_body', $this->email['email_body']);

        // if we have multiple templates, add a dropdown to select them
        if (count($this->templates) > 1) {
            $this->form->addDropdown('template', array_combine($this->templates, $this->templates), $this->email['email_template']);
        }
    }

    protected function parse(): void
    {
        parent::parse();

        $this->template->assign('item', $this->record);

        // add form name to the breadcrumb
        $this->header->appendDetailToBreadcrumbs($this->record['name']);
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
                $txtEmailToAddresses->isFilled(BL::err('FieldIsRequired'));

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
                $ddnEmailToField->isFilled(BL::err('FieldIsRequired'));
            }

            if ($this->form->isCorrect()) {
                // build array
                $values = [];
                $values['id'] = $this->id;
                $values['email_data'] = (int) $chkEmailData->isChecked();
                $values['email_subject'] = $txtEmailSubject->getValue();
                $values['email_recipient'] = $rbtRecipient->getValue();
                $values['email_to_field'] = ($rbtRecipient->getValue() == 'field') ? $ddnEmailToField->getValue() : 0;
                $values['email_to_addresses'] = ($rbtRecipient->getValue() == 'email') ? serialize($emailAddresses) : null;
                $values['email_from'] = serialize(
                    [
                        'name' => $txtFromName->getValue(),
                        'email' => $txtFromEmail->getValue(),
                    ]
                );
                $values['email_body'] = $txtEmailBody->getValue();
                $values['email_template'] = count($this->templates) > 1
                    ? $this->form->getField('template')->getValue() : $this->templates[0];
                $values['edited_on'] = BackendModel::getUTCDate();

                // insert the item
                BackendFormBuilderModel::updateEmail($values);

                // redirect
                $this->redirect(
                    BackendModel::createUrlForAction('Emails') . '&id=' . $this->formId .
                    '&report=edited-email'
                );
            }
        }
    }

    private function loadDeleteForm(): void
    {
        $deleteForm = $this->createForm(
            DeleteType::class,
            ['id' => $this->id],
            ['module' => $this->getModule(), 'action' => 'DeleteEmail']
        );
        $this->template->assign('deleteForm', $deleteForm->createView());
    }
}
