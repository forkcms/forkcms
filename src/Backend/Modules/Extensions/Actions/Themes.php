<?php

namespace Backend\Modules\Extensions\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Modules\Extensions\Engine\Model as BackendExtensionsModel;
use Backend\Modules\Pages\Engine\Model as BackendPagesModel;

/**
 * This is the themes-action, it will display the overview of modules.
 */
class Themes extends BackendBaseActionIndex
{
    /**
     * The form instance.
     *
     * @var BackendForm
     */
    private $form;

    /**
     * Theme id.
     *
     * @var int
     */
    private $id;

    /**
     * List of available themes (installed & installable)
     *
     * @var array
     */
    private $installableThemes = [];
    private $installedThemes = [];

    public function execute(): void
    {
        parent::execute();

        // load the data
        $this->loadData();

        // load the form
        $this->loadForm();
        $this->validateForm();
        $this->parse();
        $this->display();
    }

    private function loadData(): void
    {
        // loop themes
        foreach (BackendExtensionsModel::getThemes() as $theme) {
            // themes that are already installed = have at least 1 template in database
            if ($theme['installed']) {
                $this->installedThemes[] = $theme;
            } elseif ($theme['installable']) {
                // themes that are not yet installed, but contain valid info.xml installation data
                $this->installableThemes[] = $theme;
            }
        }
    }

    private function loadForm(): void
    {
        $this->form = new BackendForm('settingsThemes');

        // fetch the themes
        $themes = $this->installedThemes;

        // set selected theme
        $selected = $this->getRequest()->request->has('installedThemes')
            ? $this->getRequest()->request->get('installedThemes')
            : $this->get('fork.settings')->get('Core', 'theme', 'Fork');

        // no themes found
        if (empty($themes)) {
            $this->redirect(BackendModel::createUrlForAction('Edit') . '&amp;id=' . $this->id . '&amp;step=1&amp;error=no-themes');
        }

        // loop the templates
        foreach ($themes as &$record) {
            // reformat custom variables
            $record['variables']['thumbnail'] = $record['thumbnail'];
            $record['variables']['installed'] = $record['installed'];
            $record['variables']['installable'] = $record['installable'];

            // set selected template
            if ($record['value'] == $selected) {
                $record['variables']['selected'] = true;
            }

            // unset the variable field
            unset($record['thumbnail'], $record['installed'], $record['installable']);
        }

        // templates
        $this->form->addRadiobutton('installedThemes', $themes, $selected);
    }

    protected function parse(): void
    {
        parent::parse();

        $this->form->parse($this->template);

        // parse not yet installed themes
        $this->template->assign('installableThemes', $this->installableThemes);
    }

    private function validateForm(): void
    {
        // is the form submitted?
        if ($this->form->isSubmitted()) {
            // no errors?
            if ($this->form->isCorrect()) {
                // determine themes
                $newTheme = $this->form->getField('installedThemes')->getValue();
                $oldTheme = $this->get('fork.settings')->get('Core', 'theme', 'Fork');

                // check if we actually switched themes
                if ($newTheme != $oldTheme) {
                    // fetch templates
                    $oldTemplates = BackendExtensionsModel::getTemplates($oldTheme);
                    $newTemplates = BackendExtensionsModel::getTemplates($newTheme);

                    // check if templates already exist
                    if (empty($newTemplates)) {
                        // templates do not yet exist; don't switch
                        $this->redirect(BackendModel::createUrlForAction('Themes') . '&error=no-templates-available');

                        return;
                    }

                    // fetch current default template
                    $oldDefaultTemplatePath = $oldTemplates[$this->get('fork.settings')->get('Pages', 'default_template')]['path'];

                    // loop new templates
                    foreach ($newTemplates as $newTemplateId => $newTemplate) {
                        // check if a a similar default template exists
                        if ($newTemplate['path'] === $oldDefaultTemplatePath) {
                            // set new default id
                            $newDefaultTemplateId = (int) $newTemplateId;
                            break;
                        }
                    }

                    // no default template was found, set first template as default
                    if (!isset($newDefaultTemplateId)) {
                        $newDefaultTemplateId = array_keys($newTemplates);
                        $newDefaultTemplateId = $newDefaultTemplateId[0];
                    }

                    // update theme
                    $this->get('fork.settings')->set('Core', 'theme', $newTheme);

                    // save new default template
                    $this->get('fork.settings')->set('Pages', 'default_template', $newDefaultTemplateId);

                    // loop old templates
                    foreach ($oldTemplates as $oldTemplateId => $oldTemplate) {
                        // loop new templates
                        foreach ($newTemplates as $newTemplateId => $newTemplate) {
                            // if the templates don't match we can skip this one
                            if ($oldTemplate['path'] != $newTemplate['path']) {
                                continue;
                            }

                            // switch template
                            BackendPagesModel::updatePagesTemplates($oldTemplateId, $newTemplateId);

                            // break loop
                            continue 2;
                        }

                        // getting here meant we found no matching template for the new theme; pick first theme's template as default
                        BackendPagesModel::updatePagesTemplates($oldTemplateId, $newDefaultTemplateId);
                    }
                }

                // assign report
                $this->template->assign('report', true);
                $this->template->assign('reportMessage', BL::msg('Saved'));
            }
        }
    }
}
