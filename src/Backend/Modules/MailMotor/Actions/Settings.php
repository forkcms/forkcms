<?php

namespace Backend\Modules\MailMotor\Actions;

/*
 * This file is part of the Fork CMS MailMotor Module from SIESQO.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionIndex;
use Backend\Core\Engine\Form;
use Backend\Core\Engine\Language;
use Backend\Core\Engine\Model;
use Symfony\Component\Filesystem\Filesystem;

/**
 * This is the settings-action (default),
 * it will be used to couple your "mail-engine" account
 *
 * @author Jeroen Desloovere <jeroen@siesqo.be>
 */
final class Settings extends ActionIndex
{
    /**
     * The form instance
     *
     * @var Form
     */
    private $form;

    /**
     * Execute
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
     * Get dropdown values from mail engines
     *
     * @return array(key => value)
     */
    private function getDropdownValuesForMailEngines()
    {
        // init dropdown values
        $ddmValuesForMailEngines = array();

        // loop all container services to find "mail-engine" gateway services
        foreach ($this->getContainer()->getServiceIds() as $serviceId) {
            // the pattern to find mail engines
            $pattern = '/^mailmotor.(?P<mailengine>\w+).subscriber.gateway/';
            $matches = array();

            // we found a mail-engine gateway service
            if (preg_match($pattern, $serviceId, $matches)) {
                // we skip the fallback gateway
                if ($matches['mailengine'] == 'not_implemented') {
                    continue;
                }

                // add mailengine to dropdown values
                $ddmValuesForMailEngines[$matches['mailengine']] = ucfirst($matches['mailengine']);
            }
        }

        return $ddmValuesForMailEngines;
    }

    /**
     * Load form
     */
    public function loadForm()
    {
        $this->form = new Form('settings');

        // define dropdown values for mail engines
        $ddmValuesForMailEngines = $this->getDropdownValuesForMailEngines();

        $this->form
            ->addDropdown(
                'mail_engine',
                $ddmValuesForMailEngines,
                $this->get('fork.settings')->get($this->URL->getModule(), 'mail_engine')
            )
            ->setDefaultElement(ucfirst(Language::lbl('None')))
        ;
        $this->form->addText(
            'api_key',
            $this->get('fork.settings')->get($this->URL->getModule(), 'api_key')
        );
        $this->form->addText(
            'list_id',
            $this->get('fork.settings')->get($this->URL->getModule(), 'list_id')
        );
    }

    /**
     * Parse
     */
    protected function parse()
    {
        parent::parse();

        $this->form->parse($this->tpl);
    }

    /**
     * Validate form
     */
    private function validateForm()
    {
        if ($this->form->isSubmitted()) {
            // define fields
            $fields = $this->form->getFields();

            // define variables
            $mailEngine = $fields['mail_engine']->getValue();
            $apiKey = $fields['api_key']->getValue();
            $listId = $fields['list_id']->getValue();

            if ($mailEngine !== null) {
                $fields['api_key']->isFilled(Language::err('FieldIsRequired'));
                $fields['list_id']->isFilled(Language::err('FieldIsRequired'));
            }

            if ($this->form->isCorrect()) {
                // set our settings
                $this->get('fork.settings')->set($this->URL->getModule(), 'mail_engine', $mailEngine);

                // mail engine is empty
                if ($mailEngine == '') {
                    $this->get('fork.settings')->delete($this->URL->getModule(), 'api_key');
                    $this->get('fork.settings')->delete($this->URL->getModule(), 'list_id');
                } else {
                    $this->get('fork.settings')->set($this->URL->getModule(), 'api_key', $apiKey);
                    $this->get('fork.settings')->set($this->URL->getModule(), 'list_id', $listId);
                }

                /**
                 * We must remove our container cache after this request.
                 * Because this is not only saved in the module settings,
                 * but the compiler pass pushes this in the container.
                 * The settings cache is cleared, but the container should be cleared too,
                 * to make it rebuild with the new chosen engine
                 */
                $fs = new Filesystem();
                $fs->remove($this->getContainer()->getParameter('kernel.cache_dir'));

                // trigger event
                Model::triggerEvent(
                    $this->getModule(),
                    'after_saved_settings'
                );

                // redirect to the settings page
                $this->redirect(
                    Model::createURLForAction('Settings')
                    . '&report=saved'
                );
            }
        }
    }
}
