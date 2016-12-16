<?php

namespace Backend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Language\Language as BackendLanguage;

/**
 * This is our extended version of \SpoonDataGrid
 * This class will handle a lot of stuff for you, for example:
 *    - it will set debug mode
 *    - it will set the compile-directory
 *    - ...
 */
class DataGrid extends \SpoonDataGrid
{
    /**
     * Map of icons by given action name
     *
     * @todo this map is not full
     *
     * @var array
     */
    private $mapIcons = array(
        'add' => 'fa-plus',
        'copy' => 'fa-copy',
        'edit' => 'fa-pencil',
        'import' => 'fa-download',
        'export' => 'fa-upload',
        'delete' => 'fa-trash-o',
        'detail' => 'fa-eye',
        'details' => 'fa-eye',
        'approve' => 'fa-thumbs-o-up',
        'mark_as_spam' => 'fa-ban',
        'install' => 'fa-download',
        'use_revision' => 'fa-file-o',
        'use_draft' => 'fa-file-o',
        'custom_fields' => 'fa-tasks',
    );

    /**
     * @param \SpoonDatagridSource $source
     */
    public function __construct(\SpoonDatagridSource $source)
    {
        parent::__construct($source);

        // set debugmode, this will force the recompile for the used templates
        $this->setDebug(BackendModel::getContainer()->getParameter('kernel.debug'));

        // set the compile-directory, so compiled templates will be in a folder that is writable
        $this->setCompileDirectory(BACKEND_CACHE_PATH . '/CompiledTemplates');

        // set attributes for the datagrid
        $this->setAttributes(array(
            'class' => 'table table-hover table-striped fork-data-grid jsDataGrid'
        ));

        // id gets special treatment
        if (in_array('id', $this->getColumns())) {
            // hide the id by defaults
            $this->setColumnsHidden('id');

            // our JS needs to know an id, so we can highlight it
            $this->setRowAttributes(array('id' => 'row-[id]'));
        }

        // set default sorting options
        $this->setSortingOptions();

        // add classes on headers
        foreach ($this->getColumns() as $column) {
            // set class
            $this->setColumnHeaderAttributes($column, array('class' => $column));

            // set default label
            $this->setHeaderLabels(
                array($column => \SpoonFilter::ucfirst(BackendLanguage::lbl(\SpoonFilter::toCamelCase($column))))
            );
        }

        // set paging class
        $this->setPagingClass('Backend\Core\Engine\DataGridPaging');

        // set default template
        $this->setTemplate(BACKEND_CORE_PATH . '/Layout/Templates/Datagrid.tpl');
    }

    /**
     * Adds a new column
     *
     * @param string $name     The name for the new column.
     * @param string $label    The label for the column.
     * @param string $value    The value for the column.
     * @param string $url      The URL for the link inside the column.
     * @param string $title    A title for the image inside the column.
     * @param string $image    An URL to the image inside the column.
     * @param int    $sequence The sequence for the column.
     */
    public function addColumn(
        $name,
        $label = null,
        $value = null,
        $url = null,
        $title = null,
        $image = null,
        $sequence = null
    ) {
        // make sure we use a lowercased column in all checks
        $lowercasedName = mb_strtolower($name);

        $icon = $this->decideIcon($name);

        // known actions that should have a button
        if (in_array(
            $lowercasedName,
            array('add', 'edit', 'delete', 'detail', 'details', 'approve', 'mark_as_spam', 'install')
        )
        ) {
            // rebuild value, it should have special markup
            $value =
                '<a href="' . $url . '" class="btn btn-default btn-xs pull-right">' .
                ($icon ? '<span class="fa ' . $icon . '"></span>&nbsp;' : '') .
                $value .
                '</a>';

            // reset URL
            $url = null;
        }

        if (in_array($lowercasedName, array('use_revision', 'use_draft'))) {
            // rebuild value, it should have special markup
            $value =
                '<a href="' . $url . '" class="btn btn-default btn-xs">' .
                ($icon ? '<span class="fa ' . $icon . '"></span>&nbsp;' : '') .
                $value .
                '</a>';

            // reset URL
            $url = null;
        }

        // add the column
        parent::addColumn($name, $label, $value, $url, $title, $image, $sequence);

        // known actions
        if (in_array(
            $lowercasedName,
            array(
                'add',
                'edit',
                'delete',
                'detail',
                'details',
                'approve',
                'mark_as_spam',
                'install',
                'use_revision',
                'use_draft',
            )
        )
        ) {
            // add special attributes for actions we know
            $this->setColumnAttributes($name, array('class' => 'fork-data-grid-action action' . \SpoonFilter::toCamelCase($name)));
        }

        // set header attributes
        $this->setColumnHeaderAttributes($name, array('class' => $name));
    }

    /**
     * Adds a new column with a custom action button
     *
     * @param string $name             The name for the new column.
     * @param string $label            The label for the column.
     * @param string $value            The value for the column.
     * @param string $url              The URL for the link inside the column.
     * @param string $title            The title for the link inside the column.
     * @param array  $anchorAttributes The attributes for the anchor inside the column.
     * @param string $image            An URL to the image inside the column.
     * @param int    $sequence         The sequence for the column.
     */
    public function addColumnAction(
        $name,
        $label = null,
        $value = null,
        $url = null,
        $title = null,
        $anchorAttributes = null,
        $image = null,
        $sequence = null
    ) {
        // reserve var for attributes
        $attributes = '';

        $icon = $this->decideIcon($name);

        // no anchorAttributes set means we set the default class attribute for the anchor
        if (empty($anchorAttributes)) {
            $anchorAttributes['class'] = 'btn btn-default btn-xs';
        }

        // loop the attributes, build our attributes string
        foreach ($anchorAttributes as $attribute => $attributeValue) {
            $attributes .= ' ' . $attribute . '="' . $attributeValue . '"';
        }

        // rebuild value
        $value =
            '<a href="' . $url . '"' . $attributes . '>' .
            ($icon ? '<span class="fa ' . $icon . '"></span>&nbsp;' : '') .
            $value .
            '</a>';

        // add the column to the datagrid
        parent::addColumn($name, $label, $value, null, $title, $image, $sequence);

        // set column attributes
        $this->setColumnAttributes(
            $name,
            array(
                'class' => 'fork-data-grid-action action' . \SpoonFilter::toCamelCase($name),
                'style' => 'width: 10%;',
            )
        );

        // set header attributes
        $this->setColumnHeaderAttributes($name, array('class' => $name));
    }

    /**
     * Enable the grey out functionality. This will see if we have a column that matches our set.
     * If so, it will call the BackendDatagridFunction with the type and value so we can parse the data.
     */
    public function enableGreyingOut()
    {
        $allowedColumns = array('status', 'hidden', 'visible', 'active', 'published');
        $allColumns = $this->getColumns();

        foreach ($allowedColumns as $column) {
            // we have a match, set the row function
            if (array_search($column, $allColumns) !== false) {
                $this->setColumnHidden($column);
                $this->setRowFunction(
                    array(new DataGridFunctions(), 'greyOut'),
                    array($column, '[' . $column . ']'),
                    array($column)
                );
            }
        }
    }

    /**
     * Enable drag and drop for the current datagrid
     */
    public function enableSequenceByDragAndDrop()
    {
        // add drag and drop-class
        $this->setAttributes(array(
            'class' => 'table table-hover table-striped fork-data-grid jsDataGrid sequenceByDragAndDrop',
        ));

        // disable paging
        $this->setPaging(false);

        // hide the sequence column if present
        if ($this->hasColumn('sequence')) {
            $this->setColumnHidden('sequence');
        }

        // add a column for the handle, so users have something to hold while dragging
        $this->addColumn('dragAndDropHandle', null, '<span class="fa fa-reorder"></span>');

        // make sure the column with the handler is the first one
        $this->setColumnsSequence('dragAndDropHandle');

        // add a class on the handler column, so JS knows this is just a handler
        $this->setColumnAttributes('dragAndDropHandle', array(
            'class' => 'dragAndDropHandle fork-data-grid-sortable',
        ));

        // our JS needs to know an id, so we can send the new order
        $this->setRowAttributes(array('data-id' => '[id]'));
    }

    /**
     * Checks whether a column is present in the datagrid
     *
     * @param string $column
     *
     * @return bool
     */
    public function hasColumn($column)
    {
        return array_key_exists($column, $this->columns);
    }

    /**
     * Retrieve the parsed output.
     *
     * @return string
     */
    public function getContent()
    {
        // mass action was set
        if ($this->tpl->getAssignedValue('massAction') !== null) {
            $this->tpl->assign('footer', true);
        } elseif ($this->getPaging() && $this->getNumResults() > $this->getPagingLimit()) {
            // has paging & more than 1 page
            $this->tpl->assign(
                'footer',
                true
            );
        }

        // set the odd and even classes
        $this->setOddRowAttributes(array('class' => 'odd'));
        $this->setEvenRowAttributes(array('class' => 'even'));

        // enable greying out
        $this->enableGreyingOut();

        // execute parent
        return parent::getContent();
    }

    /**
     * Sets the active tab for this datagrid
     *
     * @param string $tab The name of the tab to show.
     */
    public function setActiveTab($tab)
    {
        $this->setURL('#' . $tab, true);
    }

    /**
     * Set a custom column confirm message.
     *
     * @param string $column   The name of the column to set the confirm for.
     * @param string $message  The message to use as a confirm message.
     * @param string $custom   Unused parameter.
     * @param string $title    The title for the column.
     * @param string $uniqueId A unique ID that will be uses.
     *
     * @throws Exception
     * @throws \SpoonDatagridException
     */
    public function setColumnConfirm($column, $message, $custom = null, $title = null, $uniqueId = '[id]')
    {
        $column = (string) $column;
        $message = (string) $message;
        $title = ($title !== null) ? (string) $title : null;
        $uniqueId = (string) $uniqueId;

        // has results
        if ($this->source->getNumResults() > 0) {
            // column doesn't exist
            if (!isset($this->columns[$column])) {
                throw new \SpoonDatagridException(
                    'The column "' . $column . '" doesn\'t exist, therefore no confirm message/script can be added.'
                );
            } else {
                // get URL
                $url = $this->columns[$column]->getURL();

                // URL provided?
                if ($url != '') {
                    // grab current value
                    $currentValue = $this->columns[$column]->getValue();

                    // reset URL
                    $this->columns[$column]->setURL(null);

                    // set the value
                    $this->columns[$column]->setValue('<a href="' . $url . '" class="">' . $currentValue . '</a>');
                }

                // generate id
                $id = 'confirm-' . (string) $uniqueId;

                // set title if there wasn't one provided
                if ($title === null) {
                    $title = \SpoonFilter::ucfirst(BackendLanguage::lbl('Delete') . '?');
                }

                // grab current value
                $value = $this->columns[$column]->getValue();

                // add class for confirmation
                if (mb_substr_count($value, '<a') > 0) {
                    if (mb_substr_count($value, 'class="') > 0) {
                        $value = str_replace(
                            'class="',
                            'data-message-id="' . $id . '" class="jsConfirmationTrigger ',
                            $value
                        );
                    } else {
                        $value = str_replace(
                            '<a ',
                            '<a data-message-id="' . $id . '" class="jsConfirmationTrigger" ',
                            $value
                        );
                    }
                } else {
                    // is it a link?
                    throw new Exception('The column doesn\'t contain a link.');
                }

                // append message
                $value .= '<div id="' . $id . '" title="' . $title . '" style="display: none;"><p>' .
                          $message . '</p></div>';

                // reset value
                $this->columns[$column]->setValue($value);
            }
        }
    }

    /**
     * Sets the column function to be executed for every row
     *
     * @param mixed $function  The function to execute.
     * @param mixed|null $arguments The arguments to pass to the function.
     * @param mixed $columns   The column wherein the result will be printed.
     * @param bool  $overwrite Should the original value be overwritten.
     */
    public function setColumnFunction($function, $arguments, $columns, $overwrite = true)
    {
        // call the parent
        parent::setColumnFunction($function, $arguments, $columns, $overwrite);

        // redefine columns
        $columns = (array) $columns;
        $attributes = null;

        // based on the function we should prepopulate the attributes array
        switch ($function) {
            // timeAgo
            case array('DataGridFunctions', 'getTimeAgo'):
                $attributes = array('class' => 'date');
                $headerAttributes = array('class' => 'date');
                break;
        }

        // add attributes if they are given
        if (!empty($attributes)) {
            // loop and set attributes
            foreach ($columns as $column) {
                $this->setColumnAttributes($column, $attributes);
            }
        }

        // add attributes if they are given
        if (!empty($headerAttributes)) {
            // loop and set attributes
            foreach ($columns as $column) {
                $this->setColumnHeaderAttributes($column, $attributes);
            }
        }
    }

    /**
     * Sets the dropdown for the mass action
     *
     * @param \SpoonFormDropdown $actionDropDown A dropdown-instance.
     */
    public function setMassAction(\SpoonFormDropdown $actionDropDown)
    {
        // build HTML
        $HTML =
            '<label for="' . $actionDropDown->getAttribute('id') . '">' .
                \SpoonFilter::ucfirst(BackendLanguage::lbl('WithSelected')) .
            '</label>' .
            $actionDropDown->parse() .
            '<button type="button" class="btn btn-default jsMassActionSubmit">' .
            '   <span>' . \SpoonFilter::ucfirst(BackendLanguage::lbl('Execute')) . '</span>' .
            '</button>';

        // assign parsed html
        $this->tpl->assign('massAction', $HTML);
    }

    /**
     * Sets the checkboxes for the mass action
     *
     * @param string $column         The name for the column that will hold the checkboxes.
     * @param string $value          The value for the checkbox.
     * @param array  $excludedValues The values that should be excluded.
     * @param array  $checkedValues  The values that should be checked.
     */
    public function setMassActionCheckboxes($column, $value, array $excludedValues = null, array $checkedValues = null)
    {
        // build label and value
        $label = '<input type="checkbox" name="toggleChecks" value="toggleChecks" />';
        $value = '<input type="checkbox" name="id[]" value="' . $value . '" class="inputCheckbox" />';

        // add the column
        $this->addColumn($column, $label, $value);

        // set as first column
        $this->setColumnsSequence($column);

        // excluded IDs found
        if (!empty($excludedValues)) {
            // fetch the datagrid attributes
            $attributes = $this->getAttributes();

            // set if needed
            if (!isset($attributes['id'])) {
                $this->setAttributes(array('id' => 'table_' . time()));
            }

            // fetch the datagrid attributes
            $attributes = $this->getAttributes();

            // build array
            $excludedData['id'] = $attributes['id'];
            $excludedData['JSON'] = json_encode($excludedValues);

            // assign the stack to the datagrid template
            $this->tpl->assign('excludedCheckboxesData', $excludedData);
        }

        // checked IDs found
        if (!empty($checkedValues)) {
            // fetch the datagrid attributes
            $attributes = $this->getAttributes();

            // set if needed
            if (!isset($attributes['id'])) {
                $this->setAttributes(array('id' => 'table_' . time()));
            }

            // fetch the datagrid attributes
            $attributes = $this->getAttributes();

            // build array
            $checkedData['id'] = $attributes['id'];
            $checkedData['JSON'] = json_encode($checkedValues);

            // assign the stack to the datagrid template
            $this->tpl->assign('checkedCheckboxesData', $checkedData);
        }
    }

    /**
     * Sets all the default settings needed when attempting to use sorting
     */
    private function setSortingOptions()
    {
        // default URL
        if (BackendModel::getContainer()->get('url')) {
            $this->setURL(
                BackendModel::createURLForAction(
                    null,
                    null,
                    null,
                    array('offset' => '[offset]', 'order' => '[order]', 'sort' => '[sort]'),
                    false
                )
            );
        }

        // sorting labels
        $this->setSortingLabels(
            BackendLanguage::lbl('SortAscending'),
            BackendLanguage::lbl('SortedAscending'),
            BackendLanguage::lbl('SortDescending'),
            BackendLanguage::lbl('SortedDescending')
        );
    }

    /**
     * Set a tooltip
     *
     * @param string $column  The name of the column to set the tooltop for.
     * @param string $message The key for the message (will be parsed through BackendLanguage::msg).
     */
    public function setTooltip($column, $message)
    {
        // get the column
        $instance = $this->getColumn($column);

        // build the value for the tooltip
        $value = BackendLanguage::msg($message);

        // reset the label
        $instance->setLabel(
            $instance->getLabel(
            ) . '<abbr class="help">?</abbr><span class="tooltip hidden" style="display: none;">' . $value . '</span>'
        );
    }

    /**
     * Sets an URL, optionally only appending the provided piece
     *
     * @param string $url    The URL to set.
     * @param bool   $append Should it be appended to the existing URL.
     */
    public function setURL($url, $append = false)
    {
        if ($append) {
            parent::setURL(parent::getURL() . $url);
        } else {
            parent::setURL($url);
        }
    }

    /**
     * Decides what icon to use by given name
     *
     * @param $name
     *
     * @return null|string
     */
    private function decideIcon($name)
    {
        $name = mb_strtolower($name);

        if (!isset($this->mapIcons[$name])) {
            return null;
        }

        return $this->mapIcons[$name];
    }
}
