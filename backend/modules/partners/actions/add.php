<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This action will add a widget to the partners module.
 *
 * @author Jelmer Prins <jelmer@sumocoders.be>
 */
use Symfony\Component\Filesystem\Filesystem;
class BackendPartnersAdd extends BackendBaseActionAdd
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        $this->loadForm();
        $this->validateForm();

        $this->parse();
        $this->display();
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        $this->frm = new BackendForm('add');
        $this->frm->addText('name', null, 255, 'inputText name', 'inputTextError name')->setAttribute('required');
    }

    /**
     * Validate the form
     */
    private function validateForm()
    {
        if ($this->frm->isSubmitted()) {
            $this->frm->cleanupFields();

            // validation
            $this->frm->getField('name')->isFilled(BL::err('NameIsRequired'));

            // no errors?
            if ($this->frm->isCorrect()) {
                $item['name'] = $this->frm->getField('name')->getValue();
                $item['id'] = BackendPartnersModel::insertWidget($item);

                //create img dir
                $fs = new Filesystem();
                $fs->mkdir(
                    FRONTEND_FILES_PATH . '/' . FrontendPartnersModel::IMAGE_PATH . '/' . $item['id'] . '/48x48'
                );

                // everything is saved, so redirect to the overview
                $this->redirect(
                    BackendModel::createURLForAction('edit', null, null, array(
                        'id' => $item['id']
                    ))
                );
            }
        }
    }
}
