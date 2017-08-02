<?php

namespace Backend\Modules\Extensions\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionAdd as BackendBaseActionAdd;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Extensions\Engine\Model as BackendExtensionsModel;

/**
 * This is the add-action, it will display a form to create a new item
 */
class AddThemeTemplate extends BackendBaseActionAdd
{
    /**
     * All available themes.
     *
     * @var array
     */
    private $availableThemes = [];

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

    /**
     * The theme we are adding a template for.
     *
     * @var string
     */
    private $selectedTheme;

    public function execute(): void
    {
        parent::execute();

        // load additional js
        $this->header->addJS('ThemeTemplate.js');

        // load data
        $this->loadData();
        $this->loadForm();
        $this->validateForm();
        $this->parse();
        $this->display();
    }

    private function loadData(): void
    {
        // get data
        $this->selectedTheme = $this->getRequest()->query->get('theme');

        // build available themes
        foreach (BackendExtensionsModel::getThemes() as $theme) {
            $this->availableThemes[$theme['value']] = $theme['label'];
        }

        // determine selected theme, based upon submitted form or default theme
        if (!array_key_exists($this->selectedTheme, $this->availableThemes)) {
            $this->selectedTheme = $this->get('fork.settings')->get('Core', 'theme', 'Fork');
        }
    }

    private function loadForm(): void
    {
        // create form
        $this->form = new BackendForm('add');

        // create elements
        $this->form->addDropdown('theme', $this->availableThemes, $this->selectedTheme);
        $this->form->addText('label');
        $this->form->addText('file');
        $this->form->addTextarea('format');
        $this->form->addCheckbox('active', true);
        $this->form->addCheckbox('default');
        $this->form->addCheckbox('image');

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
        $position['blocks'][]['formElements']['ddmType'] = $this->form->addDropdown('type_' . $position['i'] . '_' . 0, $defaultExtras, null, false, 'form-control positionBlock', 'form-control danger positionBlockError');
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
            foreach ($this->extras[$name] as $extra) {
                $position['blocks'][]['formElements']['ddmType'] = $this->form->addDropdown('type_' . $position['i'] . '_' . 0, $defaultExtras, $extra, false, 'form-control positionBlock', 'form-control danger positionBlockError');
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
            if (empty($table)) {
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

                    // doublecheck position names
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
                $item['theme'] = $this->form->getField('theme')->getValue();
                $item['label'] = $this->form->getField('label')->getValue();
                $item['path'] = 'Core/Layout/Templates/' . $this->form->getField('file')->getValue();
                $item['active'] = $this->form->getField('active')->isChecked();
                $item['data']['format'] = trim(str_replace(["\n", "\r", ' '], '', $this->form->getField('format')->getValue()));
                $item['data']['names'] = $this->names;
                $item['data']['default_extras'] = $this->extras;
                $item['data']['default_extras_' . BL::getWorkingLanguage()] = $this->extras;
                $item['data']['image'] = $this->form->getField('image')->isChecked();

                // serialize the data
                $item['data'] = serialize($item['data']);

                // insert the item
                $item['id'] = BackendExtensionsModel::insertTemplate($item);

                // set default template
                if ($this->form->getField('default')->getChecked() && $item['theme'] == $this->get('fork.settings')->get('Core', 'theme', 'Fork')) {
                    $this->get('fork.settings')->set($this->getModule(), 'default_template', $item['id']);
                }

                // everything is saved, so redirect to the overview
                $this->redirect(BackendModel::createUrlForAction('ThemeTemplates') . '&theme=' . $item['theme'] . '&report=added-template&var=' . rawurlencode($item['label']) . '&highlight=row-' . $item['id']);
            }
        }
    }
}
