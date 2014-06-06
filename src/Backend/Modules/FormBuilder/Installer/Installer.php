<?php

namespace Backend\Modules\FormBuilder\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Installer\ModuleInstaller;

/**
 * Installer for the form_builder module
 *
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class Installer extends ModuleInstaller
{
    /**
     * Install the module
     */
    public function install()
    {
        // add as a module
        $this->addModule('FormBuilder');

        // load database scheme and locale
        $this->importSQL(dirname(__FILE__) . '/Data/install.sql');
        $this->importLocale(dirname(__FILE__) . '/Data/locale.xml');

        // set rights
        $this->setModuleRights(1, 'FormBuilder');
        $this->setActionRights(1, 'FormBuilder', 'Add');
        $this->setActionRights(1, 'FormBuilder', 'Edit');
        $this->setActionRights(1, 'FormBuilder', 'Delete');
        $this->setActionRights(1, 'FormBuilder', 'Index');
        $this->setActionRights(1, 'FormBuilder', 'Data');
        $this->setActionRights(1, 'FormBuilder', 'DataDetails');
        $this->setActionRights(1, 'FormBuilder', 'MassDataAction');
        $this->setActionRights(1, 'FormBuilder', 'GetField');
        $this->setActionRights(1, 'FormBuilder', 'DeleteField');
        $this->setActionRights(1, 'FormBuilder', 'SaveField');
        $this->setActionRights(1, 'FormBuilder', 'Sequence');
        $this->setActionRights(1, 'FormBuilder', 'ExportData');

        // set navigation
        $navigationModulesId = $this->setNavigation(null, 'Modules');
        $this->setNavigation($navigationModulesId, 'FormBuilder', 'form_builder/index', array(
            'form_builder/add',
            'form_builder/edit',
            'form_builder/data',
            'form_builder/data_details'
        ));

        // get search extra id
        $extras = array();
        $extras['search'] = (int) $this->getDB()->getVar(
            'SELECT id
             FROM modules_extras
             WHERE module = ? AND type = ? AND action = ?',
            array('search', 'widget', 'form')
        );

        // loop languages
        foreach ($this->getSites() as $site) {
            foreach ($this->getLanguages($site['id']) as $language) {
                $this->insertContactPage($language, $site['id'], $extras);
            }
        }
    }

    protected function insertContactPage($language, $siteId, $extras)
    {
        $extras['contact_form'] = $this->createContactForm($language, $siteId);

        $this->insertPage(
            array(
                'title' => \SpoonFilter::ucfirst($this->getLocale('Contact', 'Core', $language, 'lbl', 'Frontend')),
                'parent_id' => 1,
                'language' => $language,
                'site_id' => $siteId
            ),
            null,
            array('html' => PATH_WWW . '/src/Backend/Modules/Pages/Installer/Data/' . $language . '/contact.txt'),
            array('extra_id' => $extras['contact_form'], 'position' => 'main'),
            array('extra_id' => $extras['search'], 'position' => 'top')
        );
    }

    /**
     * Creates a contact form for a site language combo
     * @todo: implement siteId when formbuilder is changed
     *
     * @param string $language
     * @param int $siteId
     * @return int
     */
    protected function createContactForm($language, $siteId)
    {
        // create form
        $form = array();
        $form['language'] = $language;
        $form['user_id'] = $this->getDefaultUserID();
        $form['name'] = \SpoonFilter::ucfirst($this->getLocale('Contact', 'Core', $language, 'lbl', 'Frontend'));
        $form['method'] = 'database_email';
        $form['email'] = serialize(array($this->getVariable('email')));
        $form['success_message'] = $this->getLocale('ContactMessageSent', 'Core', $language, 'msg', 'Frontend');
        $form['identifier'] = 'contact-' . $language;
        $form['created_on'] = gmdate('Y-m-d H:i:s');
        $form['edited_on'] = gmdate('Y-m-d H:i:s');
        $formId = $this->getDB()->insert('forms', $form);

        // create submit button
        $field['form_id'] = $formId;
        $field['type'] = 'submit';
        $field['settings'] = serialize(
            array(
                'values' => \SpoonFilter::ucfirst($this->getLocale('Send', 'Core', $language, 'lbl', 'Frontend'))
            )
        );
        $this->getDB()->insert('forms_fields', $field);

        // create name field
        $field['form_id'] = $formId;
        $field['type'] = 'textbox';
        $field['settings'] = serialize(
            array(
                'label' => \SpoonFilter::ucfirst($this->getLocale('Name', 'Core', $language, 'lbl', 'Frontend'))
            )
        );
        $nameId = $this->getDB()->insert('forms_fields', $field);

        // name validation
        $validate['field_id'] = $nameId;
        $validate['type'] = 'required';
        $validate['error_message'] = $this->getLocale('NameIsRequired', 'Core', $language, 'err', 'Frontend');
        $this->getDB()->insert('forms_fields_validation', $validate);

        // create email field
        $field['form_id'] = $formId;
        $field['type'] = 'textbox';
        $field['settings'] = serialize(
            array(
                'label' => \SpoonFilter::ucfirst($this->getLocale('Email', 'Core', $language, 'lbl', 'Frontend'))
            )
        );
        $emailId = $this->getDB()->insert('forms_fields', $field);

        // email validation
        $validate['field_id'] = $emailId;
        $validate['type'] = 'email';
        $validate['error_message'] = $this->getLocale('EmailIsInvalid', 'Core', $language, 'err', 'Frontend');
        $this->getDB()->insert('forms_fields_validation', $validate);

        // create message field
        $field['form_id'] = $formId;
        $field['type'] = 'textarea';
        $field['settings'] = serialize(
            array(
                'label' => \SpoonFilter::ucfirst($this->getLocale('Message', 'Core', $language, 'lbl', 'Frontend'))
            )
        );
        $messageId = $this->getDB()->insert('forms_fields', $field);

        // name validation
        $validate['field_id'] = $messageId;
        $validate['type'] = 'required';
        $validate['error_message'] = $this->getLocale('MessageIsRequired', 'Core', $language, 'err', 'Frontend');
        $this->getDB()->insert('forms_fields_validation', $validate);

        // insert extra
        return $this->insertExtra(
            'FormBuilder',
            'widget',
            'FormBuilder',
            'Form',
            serialize(
                array(
                    'language' => $form['language'],
                    'extra_label' => $form['name'],
                    'id' => $formId,
                )
            ),
            'N',
            '400' . $formId
        );
    }
}
