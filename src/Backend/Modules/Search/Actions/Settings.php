<?php

namespace Backend\Modules\Search\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Search\Engine\Model as BackendSearchModel;

/**
 * This is the settings-action, it will display a form to set general search settings
 */
class Settings extends BackendBaseActionEdit
{
    /**
     * List of modules
     *
     * @var array
     */
    private $modules = [];

    /**
     * Settings per module
     *
     * @var array
     */
    private $settings = [];

    public function execute(): void
    {
        parent::execute();
        $this->loadForm();
        $this->validateForm();
        $this->parse();
        $this->display();
    }

    private function loadForm(): void
    {
        // init settings form
        $this->form = new BackendForm('settings');

        // get current settings
        $this->settings = BackendSearchModel::getModuleSettings();

        // add field for pagination
        $this->form->addDropdown(
            'overview_num_items',
            array_combine(range(1, 30), range(1, 30)),
            $this->get('fork.settings')->get($this->getModule(), 'overview_num_items', 20)
        );
        $this->form->addDropdown(
            'autocomplete_num_items',
            array_combine(range(1, 30), range(1, 30)),
            $this->get('fork.settings')->get($this->getModule(), 'autocomplete_num_items', 20)
        );
        $this->form->addDropdown(
            'autosuggest_num_items',
            array_combine(range(1, 30), range(1, 30)),
            $this->get('fork.settings')->get($this->getModule(), 'autosuggest_num_items', 20)
        );

        // add checkbox for the sitelinks search box in Google
        $this->form->addCheckbox(
            'use_sitelinks_search_box',
            $this->get('fork.settings')->get($this->getModule(), 'use_sitelinks_search_box', true)
        );

        // modules that, no matter what, can not be searched
        $disallowedModules = ['Search'];

        // loop modules
        foreach (BackendModel::getModulesForDropDown() as $module => $label) {
            // check if module is searchable
            if (!in_array($module, $disallowedModules) &&
                method_exists('Frontend\\Modules\\' . $module . '\\Engine\\Model', 'search')
            ) {
                // add field to decide whether or not this module is searchable
                $this->form->addCheckbox(
                    'search_' . $module,
                    isset($this->settings[$module]) ? $this->settings[$module]['searchable'] : false
                );

                // add field to decide weight for this module
                $this->form->addText(
                    'search_' . $module . '_weight',
                    isset($this->settings[$module]) ? $this->settings[$module]['weight'] : 1
                );

                // field disabled?
                if (!isset($this->settings[$module]) || !$this->settings[$module]['searchable']) {
                    $this->form->getField('search_' . $module . '_weight')->setAttribute('disabled', 'disabled');
                    $this->form->getField('search_' . $module . '_weight')->setAttribute('class', 'form-control disabled');
                }

                // add to list of modules
                $this->modules[] = [
                    'module' => $module,
                    'id' => $this->form->getField('search_' . $module)->getAttribute('id'),
                    'label' => $label,
                    'chk' => $this->form->getField('search_' . $module)->parse(),
                    'txt' => $this->form->getField('search_' . $module . '_weight')->parse(),
                    'txtError' => '',
                ];
            }
        }
    }

    protected function parse(): void
    {
        parent::parse();

        // parse form
        $this->form->parse($this->template);

        // assign iteration
        $this->template->assign('modules', $this->modules);
    }

    private function validateForm(): void
    {
        // form is submitted
        if ($this->form->isSubmitted()) {
            // validate module weights
            foreach ($this->modules as $i => $module) {
                // only if this module is enabled
                if ($this->form->getField('search_' . $module['module'])->getChecked()) {
                    // valid weight?
                    $this->form->getField('search_' . $module['module'] . '_weight')->isDigital(
                        BL::err('WeightNotNumeric')
                    );
                    $this->modules[$i]['txtError'] = $this->form->getField(
                        'search_' . $module['module'] . '_weight'
                    )->getErrors();
                }
            }

            // form is validated
            if ($this->form->isCorrect()) {
                // set our settings
                $this->get('fork.settings')->set(
                    $this->getModule(),
                    'overview_num_items',
                    $this->form->getField('overview_num_items')->getValue()
                );
                $this->get('fork.settings')->set(
                    $this->getModule(),
                    'autocomplete_num_items',
                    $this->form->getField('autocomplete_num_items')->getValue()
                );
                $this->get('fork.settings')->set(
                    $this->getModule(),
                    'autosuggest_num_items',
                    $this->form->getField('autosuggest_num_items')->getValue()
                );
                $this->get('fork.settings')->set(
                    $this->getModule(),
                    'use_sitelinks_search_box',
                    $this->form->getField('use_sitelinks_search_box')->isChecked()
                );

                // module search
                foreach ((array) $this->modules as $module) {
                    $searchable = $this->form->getField('search_' . $module['module'])->getChecked();
                    $weight = $this->form->getField('search_' . $module['module'] . '_weight')->getValue();

                    BackendSearchModel::insertModuleSettings($module['module'], $searchable, $weight);
                }

                // redirect to the settings page
                $this->redirect(BackendModel::createUrlForAction('Settings') . '&report=saved');
            }
        }
    }
}
