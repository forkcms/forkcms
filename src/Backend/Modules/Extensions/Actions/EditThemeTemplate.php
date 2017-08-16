<?php

namespace Backend\Modules\Extensions\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Language\Language as BL;
use Backend\Form\Type\DeleteType;
use Backend\Modules\Extensions\Engine\Model as BackendExtensionsModel;
use Backend\Modules\Pages\Engine\Model as BackendPagesModel;

/**
 * This is the edit-action, it will display a form to edit an item
 */
class EditThemeTemplate extends BackendBaseActionEdit
{
    /**
     * The position's default extras.
     *
     * @var array
     */
    private $extras = [];

    /**
     * The position's names.
     *
     * @var array
     */
    private $names = [];

    public function execute(): void
    {
        parent::execute();
        $this->header->addJS('ThemeTemplate.js');
        $this->loadData();
        $this->loadForm();
        $this->validateForm();
        $this->loadDeleteForm();
        $this->parse();
        $this->display();
    }

    private function loadData(): void
    {
        // get record
        $this->id = $this->getRequest()->query->getInt('id');

        // validate id
        if ($this->id === 0 || !BackendExtensionsModel::existsTemplate($this->id)) {
            $this->redirect(BackendModel::createUrlForAction('ThemeTemplates') . '&error=non-existing');
        }

        // get the record
        $this->record = BackendExtensionsModel::getTemplate($this->id);

        // unserialize
        $this->record['data'] = unserialize($this->record['data']);
        $this->names = $this->record['data']['names'];
        if (isset($this->record['data']['default_extras_' . BL::getWorkingLanguage()])) {
            $this->extras = $this->record['data']['default_extras_' . BL::getWorkingLanguage()];
        } elseif (isset($this->record['data']['default_extras'])) {
            $this->extras = $this->record['data']['default_extras'];
        }

        if (!array_key_exists('image', (array) $this->record['data'])) {
            $this->record['data']['image'] = false;
        }

        // assign
        $this->template->assign('template', $this->record);

        // is the template being used
        $inUse = BackendExtensionsModel::isTemplateInUse($this->id);

        // determine if deleting is allowed
        $deleteAllowed = true;
        if ($this->record['id'] == $this->get('fork.settings')->get('Pages', 'default_template')) {
            $deleteAllowed = false;
        } elseif (count(BackendExtensionsModel::getTemplates()) == 1) {
            $deleteAllowed = false;
        } elseif ($inUse) {
            $deleteAllowed = false;
        }

        // assign
        $this->template->assign('inUse', $inUse);
        $this->template->assign('allowExtensionsDeleteThemeTemplate', $deleteAllowed);
    }

    private function loadForm(): void
    {
        // create form
        $this->form = new BackendForm('edit');

        // init var
        $defaultId = $this->get('fork.settings')->get('Pages', 'default_template');

        // build available themes
        $themes = [];
        foreach (BackendExtensionsModel::getThemes() as $theme) {
            $themes[$theme['value']] = $theme['label'];
        }

        // create elements
        $this->form->addDropdown('theme', $themes, $this->get('fork.settings')->get('Core', 'theme', 'Fork'));
        $this->form->addText('label', $this->record['label']);
        $this->form->addText('file', str_replace('Core/Layout/Templates/', '', $this->record['path']));
        $this->form->addTextarea('format', str_replace('],[', "],\n[", $this->record['data']['format']));
        $this->form->addCheckbox('active', $this->record['active']);
        $this->form->addCheckbox('default', ($this->record['id'] == $defaultId));
        $this->form->addCheckbox('overwrite', false);
        $this->form->addCheckbox('image', $this->record['data']['image']);

        // if this is the default template we can't alter the active/default state
        if (($this->record['id'] == $defaultId)) {
            $this->form->getField('active')->setAttributes(['disabled' => 'disabled']);
            $this->form->getField('default')->setAttributes(['disabled' => 'disabled']);
        }

        // if the template is in use we cant alter the active state
        if (BackendExtensionsModel::isTemplateInUse($this->id)) {
            $this->form->getField('active')->setAttributes(['disabled' => 'disabled']);
        }

        $positions = [];
        $blocks = [];
        $widgets = [];
        $extras = BackendExtensionsModel::getExtras();

        // loop extras to populate the default extras
        foreach ($extras as $item) {
            if ($item['type'] == 'block') {
                $blocks[$item['id']] = \SpoonFilter::ucfirst(BL::lbl($item['label']));
                if (isset($item['data']['extra_label'])) {
                    $blocks[$item['id']] = \SpoonFilter::ucfirst($item['data']['extra_label']);
                }
            } elseif ($item['type'] == 'widget') {
                $widgets[$item['id']] = \SpoonFilter::ucfirst(BL::lbl(\SpoonFilter::toCamelCase($item['module']))) . ': ' . \SpoonFilter::ucfirst(BL::lbl($item['label']));
                if (isset($item['data']['extra_label'])) {
                    $widgets[$item['id']] = \SpoonFilter::ucfirst(BL::lbl(\SpoonFilter::toCamelCase($item['module']))) . ': ' . $item['data']['extra_label'];
                }
            }
        }

        // sort
        asort($blocks, SORT_STRING);
        asort($widgets, SORT_STRING);

        // create array
        $defaultExtras = [
            '' => [0 => \SpoonFilter::ucfirst(BL::lbl('Editor'))],
            \SpoonFilter::ucfirst(BL::lbl('Widgets')) => $widgets,
        ];

        // create default position field
        $position = [];
        $position['i'] = 0;
        $position['formElements']['txtPosition'] = $this->form->addText('position_' . $position['i'], null, 255, 'form-control positionName', 'form-control danger positionName');
        $position['blocks'][]['formElements']['ddmType'] = $this->form->addDropdown('type_' . $position['i'] . '_' . 0, $defaultExtras, null, false, 'form-control positionBlock', 'form-control positionBlockError');
        $positions[] = $position;

        // content has been submitted: re-create submitted content rather than the database-fetched content
        if ($this->getRequest()->request->has('position_0')) {
            $this->names = [];
            $this->extras = [];
            $i = 1;
            $errors = [];

            // loop submitted positions
            while ($this->getRequest()->request->has('position_' . $i)) {
                $j = 0;
                $extras = [];

                // gather position names
                $name = $this->getRequest()->request->get('position_' . $i);

                // loop submitted blocks
                while ($this->getRequest()->request->has('type_' . $i . '_' . $j)) {
                    // gather blocks id
                    $extras[] = $this->getRequest()->request->getInt('type_' . $i . '_' . $j);

                    // increment counter; go fetch next block
                    ++$j;
                }

                // increment counter; go fetch next position
                ++$i;

                // position already exists -> error
                if (in_array($name, $this->names)) {
                    $errors[] = sprintf(BL::getError('DuplicatePositionName'), $name);
                }

                // position name == fallback -> error
                if ($name == 'fallback') {
                    $errors[] = sprintf(BL::getError('ReservedPositionName'), $name);
                }

                // not alphanumeric -> error
                if (!\SpoonFilter::isValidAgainstRegexp('/^[a-z0-9]+$/i', $name)) {
                    $errors[] = sprintf(BL::getError('NoAlphaNumPositionName'), $name);
                }

                // save positions
                $this->names[] = $name;
                $this->extras[$name] = $extras;
            }

            // add errors
            if (!empty($errors)) {
                $this->form->addError(implode('<br />', array_unique($errors)));
            }
        }

        // build blocks array
        foreach ($this->names as $i => $name) {
            // create default position field
            $position = [];
            $position['i'] = $i + 1;
            $position['formElements']['txtPosition'] = $this->form->addText('position_' . $position['i'], $name, 255, 'form-control positionName', 'form-control danger positionName');

            if (isset($this->extras[$name])) {
                foreach ($this->extras[$name] as $y => $extra) {
                    $position['blocks'][]['formElements']['ddmType'] = $this->form->addDropdown('type_' . $position['i'] . '_' . $y, $defaultExtras, $extra, false, 'form-control positionBlock', 'form-control positionBlockError');
                }
            }

            $positions[] = $position;
        }

        // assign
        $this->template->assign('positions', $positions);
    }

    protected function parse(): void
    {
        parent::parse();

        // assign form errors
        $this->template->assign('formErrors', (string) $this->form->getErrors());
    }

    private function validateForm(): void
    {
        // is the form submitted?
        if ($this->form->isSubmitted()) {
            // cleanup the submitted fields, ignore fields that were added by hackers
            $this->form->cleanupFields();

            // required fields
            $this->form->getField('file')->isFilled(BL::err('FieldIsRequired'));
            $this->form->getField('label')->isFilled(BL::err('FieldIsRequired'));
            $this->form->getField('format')->isFilled(BL::err('FieldIsRequired'));

            $templateFile = $this->getContainer()->getParameter('site.path_www');
            // check if the template file exists
            $templateFile .= '/src/Frontend/Themes/' . $this->form->getField('theme')->getValue() . '/Core/Layout/Templates/' . $this->form->getField('file')->getValue();
            if (!is_file($templateFile)) {
                $this->form->getField('file')->addError(BL::err('TemplateFileNotFound'));
            }

            // validate syntax
            $syntax = trim(str_replace(["\n", "\r", ' '], '', $this->form->getField('format')->getValue()));

            // init var
            $table = BackendExtensionsModel::templateSyntaxToArray($syntax);

            // validate the syntax
            if ($table === false) {
                $this->form->getField('format')->addError(BL::err('InvalidTemplateSyntax'));
            } else {
                $html = BackendExtensionsModel::buildTemplateHTML($syntax);
                $cellCount = 0;
                $first = true;
                $errors = [];

                // loop rows
                foreach ($table as $row) {
                    // first row defines the cellcount
                    if ($first) {
                        $cellCount = count($row);
                    }

                    // not same number of cells
                    if (count($row) != $cellCount) {
                        // add error
                        $errors[] = BL::err('InvalidTemplateSyntax');

                        // stop
                        break;
                    }

                    // double check position names
                    foreach ($row as $cell) {
                        // ignore unavailable space
                        if ($cell != '/') {
                            // not alphanumeric -> error
                            if (!in_array($cell, $this->names)) {
                                $errors[] = sprintf(BL::getError('NonExistingPositionName'), $cell);
                            } elseif (mb_substr_count($html, '"#position-' . $cell . '"') != 1) {
                                // can't build proper html -> error
                                $errors[] = BL::err('InvalidTemplateSyntax');
                            }
                        }
                    }

                    // reset
                    $first = false;
                }

                // add errors
                if (!empty($errors)) {
                    $this->form->getField('format')->addError(implode('<br />', array_unique($errors)));
                }
            }

            // no errors?
            if ($this->form->isCorrect()) {
                // build array
                $item = [];
                $item['id'] = $this->id;
                $item['theme'] = $this->form->getField('theme')->getValue();
                $item['label'] = $this->form->getField('label')->getValue();
                $item['path'] = 'Core/Layout/Templates/' . $this->form->getField('file')->getValue();
                $item['active'] = $this->form->getField('active')->isChecked();

                // copy data from previous version, otherwise default_extras from other languages are overwritten
                $item['data'] = $this->record['data'];
                $item['data']['format'] = trim(str_replace(["\n", "\r", ' '], '', $this->form->getField('format')->getValue()));
                $item['data']['names'] = $this->names;
                $item['data']['default_extras'] = $this->extras;
                $item['data']['default_extras_' . BL::getWorkingLanguage()] = $this->extras;
                $item['data']['image'] = $this->form->getField('image')->isChecked();

                // serialize
                $item['data'] = serialize($item['data']);

                // if this is the default template make the template active
                if ($this->get('fork.settings')->get('Pages', 'default_template') == $this->record['id']) {
                    $item['active'] = true;
                }

                // if the template is in use we can't de-activate it
                if (BackendExtensionsModel::isTemplateInUse($item['id'])) {
                    $item['active'] = true;
                }

                // insert the item
                BackendExtensionsModel::updateTemplate($item);

                // set default template
                if ($this->form->getField('default')->getChecked() && $item['theme'] == $this->get('fork.settings')->get('Core', 'theme', 'Fork')) {
                    $this->get('fork.settings')->set('pages', 'default_template', $item['id']);
                }

                // update all existing pages using this template to add the newly inserted block(s)
                if (BackendExtensionsModel::isTemplateInUse($item['id'])) {
                    BackendPagesModel::updatePagesTemplates($item['id'], $item['id'], $this->form->getField('overwrite')->getChecked());
                }

                // everything is saved, so redirect to the overview
                $this->redirect(BackendModel::createUrlForAction('ThemeTemplates') . '&theme=' . $item['theme'] . '&report=edited-template&var=' . rawurlencode($item['label']) . '&highlight=row-' . $item['id']);
            }
        }
    }

    private function loadDeleteForm(): void
    {
        $deleteForm = $this->createForm(
            DeleteType::class,
            ['id' => $this->record['id']],
            ['module' => $this->getModule(), 'action' => 'DeleteThemeTemplate']
        );
        $this->template->assign('deleteForm', $deleteForm->createView());
    }
}
