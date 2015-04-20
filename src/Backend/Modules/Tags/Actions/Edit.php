<?php

namespace Backend\Modules\Tags\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Common\Uri as CommonUri;

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\DataGridArray as BackendDataGridArray;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Tags\Engine\Model as BackendTagsModel;

/**
 * This is the edit action, it will display a form to edit an existing tag.
 *
 * @author Dave Lens <dave.lens@netlash.com>
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 */
class Edit extends BackendBaseActionEdit
{
    /**
     * DataGrid with the articles linked to the current tag
     *
     * @var    BackendDataGridArray
     */
    protected $dgUsage;

    /**
     * @var Tag
     */
    protected $tag;

    /**
     * Execute the action
     */
    public function execute()
    {
        $this->id = $this->getParameter('id', 'int');

        // does the item exist
        if ($this->id !== null && BackendTagsModel::exists($this->id)) {
            parent::execute();
            $this->getData();
            $this->loadDataGrid();
            $this->loadForm();
            $this->validateForm();
            $this->parse();
            $this->display();
        } else {
            $this->redirect(BackendModel::createURLForAction('Index') . '&error=non-existing');
        }
    }

    /**
     * Get the data
     */
    private function getData()
    {
        $this->tag = BackendTagsModel::get($this->id);
    }

    /**
     * Load the datagrid
     */
    private function loadDataGrid()
    {
        // init var
        $items = array();

        // get modules
        $modules = BackendModel::getModules();

        // loop modules
        foreach ($modules as $module) {
            // build class name
            $className = 'Backend\\Modules\\' . $module . '\\Engine\\Model';
            if ($module == 'Core') {
                $className = 'Backend\\Core\\Engine\\Model';
            }

            // check if the getByTag-method is available
            if (is_callable(array($className, 'getByTag'))) {
                // make the call and get the item
                $moduleItems = (array) call_user_func(array($className, 'getByTag'), $this->id);

                // loop items
                foreach ($moduleItems as $row) {
                    // check if needed fields are available
                    if (isset($row['url'], $row['name'], $row['module'])) {
                        // add
                        $items[] = array(
                            'module' => \SpoonFilter::ucfirst(BL::lbl(\SpoonFilter::toCamelCase($row['module']))),
                            'name' => $row['name'],
                            'url' => $row['url']
                        );
                    }
                }
            }
        }

        // create datagrid
        $this->dgUsage = new BackendDataGridArray($items);
        $this->dgUsage->setPaging(false);
        $this->dgUsage->setColumnsHidden(array('url'));
        $this->dgUsage->setHeaderLabels(array('name' => \SpoonFilter::ucfirst(BL::lbl('Title')), 'url' => ''));
        $this->dgUsage->setColumnURL('name', '[url]', \SpoonFilter::ucfirst(BL::lbl('Edit')));
        $this->dgUsage->addColumn('edit', null, \SpoonFilter::ucfirst(BL::lbl('Edit')), '[url]', BL::lbl('Edit'));
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        $this->frm = new BackendForm('edit');
        $this->frm->addText('name', $this->tag->getName());
    }

    /**
     * Parse the form
     */
    protected function parse()
    {
        parent::parse();

        // assign id, name
        $this->tpl->assign('id', $this->id);
        $this->tpl->assign('name', $this->tag->getName());

        // assign usage-datagrid
        $this->tpl->assign('usage', (string) $this->dgUsage->getContent());
    }

    /**
     * Validate the form
     */
    private function validateForm()
    {
        // is the form submitted?
        if ($this->frm->isSubmitted()) {
            // cleanup the submitted fields, ignore fields that were added by hackers
            $this->frm->cleanupFields();

            // validate fields
            $this->frm->getField('name')->isFilled(BL::err('NameIsRequired'));

            // no errors?
            if ($this->frm->isCorrect()) {
                $this->tag->setName($this->frm->getField('name')->getValue());
                $this->tag->setUrl(BackendTagsModel::getURL(
                    CommonUri::getUrl(\SpoonFilter::htmlspecialcharsDecode($this->tag->getName())),
                    $this->id
                ));

                // update the item
                BackendTagsModel::update($this->tag);

                // trigger event
                BackendModel::triggerEvent($this->getModule(), 'after_edit', array('item' => $this->tag));

                // everything is saved, so redirect to the overview
                $this->redirect(
                    BackendModel::createURLForAction('Index') . '&report=edited&var=' . urlencode(
                        $this->tag->getName()
                    ) . '&highlight=row-' . $this->tag->getId()
                );
            }
        }
    }
}
