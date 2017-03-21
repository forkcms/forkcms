<?php

namespace Backend\Modules\Pages\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Authentication;
use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\DataGridDB as BackendDataGridDB;
use Backend\Core\Engine\DataGridFunctions as BackendDataGridFunctions;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Meta as BackendMeta;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Extensions\Engine\Model as BackendExtensionsModel;
use Backend\Modules\Pages\Engine\Model as BackendPagesModel;
use Backend\Modules\Search\Engine\Model as BackendSearchModel;
use Backend\Modules\Tags\Engine\Model as BackendTagsModel;
use Backend\Modules\Profiles\Engine\Model as BackendProfilesModel;

/**
 * This is the edit-action, it will display a form to update an item
 */
class Edit extends BackendBaseActionEdit
{
    /**
     * The blocks linked to this page
     *
     * @var array
     */
    private $blocksContent = array();

    /**
     * DataGrid for the drafts
     *
     * @var BackendDataGridDB
     */
    private $dgDrafts;

    /**
     * The extras
     *
     * @var array
     */
    private $extras = array();

    /**
     * Is the current user a god user?
     *
     * @var bool
     */
    private $isGod = false;

    /**
     * The positions
     *
     * @var array
     */
    private $positions = array();

    /**
     * The template data
     *
     * @var array
     */
    private $templates = array();

    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        // load record
        $this->loadData();

        // add js
        $this->header->addJS('jstree/jquery.tree.js', null, false);
        $this->header->addJS('jstree/lib/jquery.cookie.js', null, false);
        $this->header->addJS('jstree/plugins/jquery.tree.cookie.js', null, false);

        // get the templates
        $this->templates = BackendExtensionsModel::getTemplates();

        // set the default template as checked
        $this->templates[$this->record['template_id']]['checked'] = true;

        // homepage?
        if ($this->id == 1) {
            // loop and set disabled state
            foreach ($this->templates as &$row) {
                $row['disabled'] = ($row['has_block']);
            }
        }

        // get the extras
        $this->extras = BackendExtensionsModel::getExtras();

        $this->loadForm();
        $this->loadDrafts();
        $this->loadRevisions();
        $this->validateForm();
        $this->parse();
        $this->display();
    }

    /**
     * Load the record
     */
    private function loadData()
    {
        // get record
        $this->id = $this->getParameter('id', 'int');
        $this->isGod = BackendAuthentication::getUser()->isGod();

        // check if something went wrong
        if ($this->id === null || !BackendPagesModel::exists($this->id)) {
            $this->redirect(
                BackendModel::createURLForAction('Index') . '&error=non-existing'
            );
        }

        // get the record
        $this->record = BackendPagesModel::get($this->id);

        // load blocks
        $this->blocksContent = BackendPagesModel::getBlocks($this->id, $this->record['revision_id']);

        // is there a revision specified?
        $revisionToLoad = $this->getParameter('revision', 'int');

        // if this is a valid revision
        if ($revisionToLoad !== null) {
            // overwrite the current record
            $this->record = (array) BackendPagesModel::get($this->id, $revisionToLoad);

            // load blocks
            $this->blocksContent = BackendPagesModel::getBlocks($this->id, $revisionToLoad);

            // show warning
            $this->tpl->assign('appendRevision', true);
        }

        // is there a revision specified?
        $draftToLoad = $this->getParameter('draft', 'int');

        // if this is a valid revision
        if ($draftToLoad !== null) {
            // overwrite the current record
            $this->record = (array) BackendPagesModel::get($this->id, $draftToLoad);

            // load blocks
            $this->blocksContent = BackendPagesModel::getBlocks($this->id, $draftToLoad);

            // show warning
            $this->tpl->assign('appendRevision', true);
        }

        // reset some vars
        $this->record['full_url'] = BackendPagesModel::getFullURL($this->record['id']);
        $this->record['is_hidden'] = ($this->record['hidden'] == 'Y');
    }

    /**
     * Load the datagrid with drafts
     */
    private function loadDrafts()
    {
        // create datagrid
        $this->dgDrafts = new BackendDataGridDB(
            BackendPagesModel::QRY_DATAGRID_BROWSE_SPECIFIC_DRAFTS,
            array($this->record['id'], 'draft', BL::getWorkingLanguage())
        );

        // hide columns
        $this->dgDrafts->setColumnsHidden(array('id', 'revision_id'));

        // disable paging
        $this->dgDrafts->setPaging(false);

        // set headers
        $this->dgDrafts->setHeaderLabels(
            array(
                 'user_id' => \SpoonFilter::ucfirst(BL::lbl('By')),
                 'edited_on' => \SpoonFilter::ucfirst(BL::lbl('LastEditedOn')),
            )
        );

        // set column-functions
        $this->dgDrafts->setColumnFunction(array(new BackendDataGridFunctions(), 'getUser'), array('[user_id]'), 'user_id');
        $this->dgDrafts->setColumnFunction(
            array(new BackendDataGridFunctions(), 'getTimeAgo'),
            array('[edited_on]'),
            'edited_on'
        );

        // our JS needs to know an id, so we can highlight it
        $this->dgDrafts->setRowAttributes(array('id' => 'row-[revision_id]'));

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('Edit')) {
            // set column URLs
            $this->dgDrafts->setColumnURL(
                'title',
                BackendModel::createURLForAction('Edit') . '&amp;id=[id]&amp;draft=[revision_id]'
            );

            // add use column
            $this->dgDrafts->addColumn(
                'use_draft',
                null,
                BL::lbl('UseThisDraft'),
                BackendModel::createURLForAction('Edit') . '&amp;id=[id]&amp;draft=[revision_id]',
                BL::lbl('UseThisDraft')
            );
        }
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        // get default template id
        $defaultTemplateId = $this->get('fork.settings')->get('Pages', 'default_template', 1);

        // create form
        $this->frm = new BackendForm('edit');

        // assign in template
        $this->tpl->assign('defaultTemplateId', $defaultTemplateId);

        // create elements
        $this->frm->addText('title', $this->record['title'], null, 'form-control title', 'form-control danger title');
        $this->frm->addEditor('html');
        $this->frm->addHidden('template_id', $this->record['template_id']);
        $this->frm->addRadiobutton(
            'hidden',
            array(
                 array('label' => BL::lbl('Hidden'), 'value' => 'Y'),
                 array('label' => BL::lbl('Published'), 'value' => 'N'),
            ),
            $this->record['hidden']
        );

        // image related fields
        $this->frm->addImage('image');
        $this->frm->addCheckbox('remove_image');

        // page auth related fields
        // check if profiles module is installed
        if (BackendModel::isModuleInstalled('Profiles')) {
            // add checkbox for auth_required
            $this->frm->addCheckbox(
                'auth_required',
                isset($this->record['data']['auth_required']) && $this->record['data']['auth_required']
            );

            // add checkbox for index page to search
            $this->frm->addCheckbox(
                'remove_from_search_index',
                isset($this->record['data']['remove_from_search_index']) && $this->record['data']['remove_from_search_index']
            );

            // get all groups and parse them in key value pair
            $groupItems = BackendProfilesModel::getGroups();
            if (!empty($groupItems)) {
                $groups = array();
                foreach ($groupItems as $key => $item) {
                    $groups[] = array('label' => $item, 'value' => $key);
                }
                // set checked values
                $checkedGroups = array();
                if (isset($this->record['data']['auth_groups']) && is_array($this->record['data']['auth_groups'])) {
                    foreach ($this->record['data']['auth_groups'] as $group) {
                        $checkedGroups[] = $group;
                    }
                }
                // add multi checkbox
                $this->frm->addMultiCheckbox('auth_groups', $groups, $checkedGroups);
            }
        }

        // a god user should be able to adjust the detailed settings for a page easily
        if ($this->isGod) {
            // init some vars
            $items = array('move', 'children', 'edit', 'delete');
            $checked = array();
            $values = array();

            foreach ($items as $value) {
                $values[] = array('label' => BL::msg(\SpoonFilter::toCamelCase('allow_' . $value)), 'value' => $value);
                if (isset($this->record['allow_' . $value]) && $this->record['allow_' . $value] == 'Y') {
                    $checked[] = $value;
                }
            }

            $this->frm->addMultiCheckbox('allow', $values, $checked);
        }

        // build prototype block
        $block['index'] = 0;
        $block['formElements']['chkVisible'] = $this->frm->addCheckbox('block_visible_' . $block['index'], true);
        $block['formElements']['hidExtraId'] = $this->frm->addHidden('block_extra_id_' . $block['index'], 0);
        $block['formElements']['hidPosition'] = $this->frm->addHidden('block_position_' . $block['index'], 'fallback');
        $block['formElements']['txtHTML'] = $this->frm->addTextarea(
            'block_html_' . $block['index'],
            ''
        ); // this is no editor; we'll add the editor in JS

        // add default block to "fallback" position, the only one which we can rest assured to exist
        $this->positions['fallback']['blocks'][] = $block;

        // content has been submitted: re-create submitted content rather than the db-fetched content
        if (isset($_POST['block_html_0'])) {
            // init vars
            $this->blocksContent = array();
            $hasBlock = false;
            $i = 1;

            // loop submitted blocks
            while (isset($_POST['block_position_' . $i])) {
                // init var
                $block = array();

                // save block position
                $block['position'] = $_POST['block_position_' . $i];
                $positions[$block['position']][] = $block;

                // set linked extra
                $block['extra_id'] = $_POST['block_extra_id_' . $i];

                // reset some stuff
                if ($block['extra_id'] <= 0) {
                    $block['extra_id'] = null;
                }

                // init html
                $block['html'] = null;

                // extra-type is HTML
                if ($block['extra_id'] === null) {
                    // reset vars
                    $block['extra_id'] = null;
                    $block['html'] = $_POST['block_html_' . $i];
                } else {
                    // type of block
                    if (isset($this->extras[$block['extra_id']]['type']) && $this->extras[$block['extra_id']]['type'] == 'block') {
                        // set error
                        if ($hasBlock) {
                            $this->frm->addError(BL::err('CantAdd2Blocks'));
                        }

                        // home can't have blocks
                        if ($this->record['id'] == 1) {
                            $this->frm->addError(BL::err('HomeCantHaveBlocks'));
                        }

                        // reset var
                        $hasBlock = true;
                    }
                }

                // set data
                $block['created_on'] = BackendModel::getUTCDate();
                $block['edited_on'] = $block['created_on'];
                $block['visible'] = isset($_POST['block_visible_' . $i]) && $_POST['block_visible_' . $i] == 'Y' ? 'Y' : 'N';
                $block['sequence'] = count($positions[$block['position']]) - 1;

                // add to blocks
                $this->blocksContent[] = $block;

                // increment counter; go fetch next block
                ++$i;
            }
        }

        // build blocks array
        foreach ($this->blocksContent as $i => $block) {
            $block['index'] = $i + 1;
            $block['formElements']['chkVisible'] = $this->frm->addCheckbox(
                'block_visible_' . $block['index'],
                $block['visible'] == 'Y'
            );
            $block['formElements']['hidExtraId'] = $this->frm->addHidden(
                'block_extra_id_' . $block['index'],
                (int) $block['extra_id']
            );
            $block['formElements']['hidPosition'] = $this->frm->addHidden(
                'block_position_' . $block['index'],
                $block['position']
            );
            $block['formElements']['txtHTML'] = $this->frm->addTextarea(
                'block_html_' . $block['index'],
                $block['html']
            ); // this is no editor; we'll add the editor in JS

            $this->positions[$block['position']]['blocks'][] = $block;
        }

        // redirect
        $redirectValue = 'none';
        if (isset($this->record['data']['internal_redirect']['page_id'])) {
            $redirectValue = 'internal';
        }
        if (isset($this->record['data']['external_redirect']['url'])) {
            $redirectValue = 'external';
        }
        $redirectValues = array(
            array('value' => 'none', 'label' => \SpoonFilter::ucfirst(BL::lbl('None'))),
            array(
                'value' => 'internal',
                'label' => \SpoonFilter::ucfirst(BL::lbl('InternalLink')),
                'variables' => array('isInternal' => true),
            ),
            array(
                'value' => 'external',
                'label' => \SpoonFilter::ucfirst(BL::lbl('ExternalLink')),
                'variables' => array('isExternal' => true),
            ),
        );
        $this->frm->addRadiobutton('redirect', $redirectValues, $redirectValue);
        $this->frm->addDropdown(
            'internal_redirect',
            BackendPagesModel::getPagesForDropdown(),
            ($redirectValue == 'internal') ? $this->record['data']['internal_redirect']['page_id'] : null
        );
        $this->frm->addText(
            'external_redirect',
            ($redirectValue == 'external') ? urldecode($this->record['data']['external_redirect']['url']) : null,
            null,
            null,
            null,
            true
        );

        // page info
        $this->frm->addCheckbox('navigation_title_overwrite', ($this->record['navigation_title_overwrite'] == 'Y'));
        $this->frm->addText('navigation_title', $this->record['navigation_title']);

        if ($this->showTags()) {
            // tags
            $this->frm->addText(
                'tags',
                BackendTagsModel::getTags($this->URL->getModule(), $this->id),
                null,
                'form-control js-tags-input',
                'error js-tags-input'
            );
        }

        // a specific action
        $isAction = (isset($this->record['data']['is_action']) && $this->record['data']['is_action'] == true) ? true : false;
        $this->frm->addCheckbox('is_action', $isAction);

        // extra
        $blockTypes = BackendPagesModel::getTypes();
        $this->frm->addDropdown('extra_type', $blockTypes, key($blockTypes));

        // meta
        $this->meta = new BackendMeta($this->frm, $this->record['meta_id'], 'title', true);

        // set callback for generating an unique URL
        $this->meta->setURLCallback(
            'Backend\Modules\Pages\Engine\Model',
            'getURL',
            array($this->record['id'], $this->record['parent_id'], $isAction)
        );
    }

    /**
     * Load the datagrid
     */
    private function loadRevisions()
    {
        // create datagrid
        $this->dgRevisions = new BackendDataGridDB(
            BackendPagesModel::QRY_BROWSE_REVISIONS,
            array(
                 $this->id,
                 'archive',
                 BL::getWorkingLanguage(
                 ),
            )
        );

        // hide columns
        $this->dgRevisions->setColumnsHidden(array('id', 'revision_id'));

        // disable paging
        $this->dgRevisions->setPaging(false);

        // set headers
        $this->dgRevisions->setHeaderLabels(
            array(
                 'user_id' => \SpoonFilter::ucfirst(BL::lbl('By')),
                 'edited_on' => \SpoonFilter::ucfirst(BL::lbl('LastEditedOn')),
            )
        );

        // set functions
        $this->dgRevisions->setColumnFunction(
            array(new BackendDataGridFunctions(), 'getUser'),
            array('[user_id]'),
            'user_id'
        );
        $this->dgRevisions->setColumnFunction(
            array(new BackendDataGridFunctions(), 'getTimeAgo'),
            array('[edited_on]'),
            'edited_on'
        );

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('Edit')) {
            // set column URLs
            $this->dgRevisions->setColumnURL(
                'title',
                BackendModel::createURLForAction('Edit') . '&amp;id=[id]&amp;revision=[revision_id]'
            );

            // add use column
            $this->dgRevisions->addColumn(
                'use_revision',
                null,
                BL::lbl('UseThisVersion'),
                BackendModel::createURLForAction('Edit') . '&amp;id=[id]&amp;revision=[revision_id]',
                BL::lbl('UseThisVersion')
            );
        }
    }

    /**
     * Parse
     */
    protected function parse()
    {
        parent::parse();

        // set
        $this->record['url'] = $this->meta->getURL();
        if ($this->id == 1) {
            $this->record['url'] = '';
        }

        // parse some variables
        $this->tpl->assign('item', $this->record);
        $this->tpl->assign('isGod', $this->isGod);
        $this->tpl->assign('templates', $this->templates);
        $this->tpl->assign('positions', $this->positions);
        $this->tpl->assign('extrasData', json_encode(BackendExtensionsModel::getExtrasData()));
        $this->tpl->assign('extrasById', json_encode(BackendExtensionsModel::getExtras()));
        $this->tpl->assign('prefixURL', rtrim(BackendPagesModel::getFullURL($this->record['parent_id']), '/'));
        $this->tpl->assign('formErrors', (string) $this->frm->getErrors());
        $this->tpl->assign('showTags', $this->showTags());

        // init var
        $showDelete = true;

        // has children?
        if (BackendPagesModel::getFirstChildId($this->record['id']) !== false) {
            $showDelete = false;
        }
        if (!$this->record['delete_allowed']) {
            $showDelete = false;
        }

        // allowed?
        if (!BackendAuthentication::isAllowedAction('Delete', $this->getModule())) {
            $showDelete = false;
        }

        // show delete button
        $this->tpl->assign('allowPagesDelete', $showDelete);

        // assign template
        $this->tpl->assignArray($this->templates[$this->record['template_id']], 'template');

        // parse datagrids
        $this->tpl->assign(
            'revisions',
            ($this->dgRevisions->getNumResults() != 0) ? $this->dgRevisions->getContent() : false
        );
        $this->tpl->assign('drafts', ($this->dgDrafts->getNumResults() != 0) ? $this->dgDrafts->getContent() : false);

        // parse the tree
        $this->tpl->assign('tree', BackendPagesModel::getTreeHTML());

        // assign if profiles module is installed
        $this->tpl->assign('showAuthenticationTab', BackendModel::isModuleInstalled('Profiles'));
    }

    /**
     * Validate the form
     */
    private function validateForm()
    {
        // is the form submitted?
        if ($this->frm->isSubmitted()) {
            // get the status
            $status = \SpoonFilter::getPostValue('status', array('active', 'draft'), 'active');

            // validate redirect
            $redirectValue = $this->frm->getField('redirect')->getValue();
            if ($redirectValue == 'internal') {
                $this->frm->getField('internal_redirect')->isFilled(
                    BL::err('FieldIsRequired')
                );
            }
            if ($redirectValue == 'external') {
                $this->frm->getField('external_redirect')->isURL(BL::err('InvalidURL'));
            }

            // set callback for generating an unique URL
            $this->meta->setURLCallback(
                'Backend\Modules\Pages\Engine\Model',
                'getURL',
                array($this->record['id'], $this->record['parent_id'], $this->frm->getField('is_action')->getChecked())
            );

            // cleanup the submitted fields, ignore fields that were added by hackers
            $this->frm->cleanupFields();

            // validate fields
            $this->frm->getField('title')->isFilled(BL::err('TitleIsRequired'));

            // validate meta
            $this->meta->validate();

            // no errors?
            if ($this->frm->isCorrect()) {
                // init var
                $data = null;
                $templateId = (int) $this->frm->getField('template_id')->getValue();

                // build data
                if ($this->frm->getField('is_action')->isChecked()) {
                    $data['is_action'] = true;
                }
                if ($redirectValue == 'internal') {
                    $data['internal_redirect'] = array(
                        'page_id' => $this->frm->getField('internal_redirect')->getValue(),
                        'code' => '301',
                    );
                }
                if ($redirectValue == 'external') {
                    $data['external_redirect'] = array(
                        'url' => BackendPagesModel::getEncodedRedirectURL(
                            $this->frm->getField('external_redirect')->getValue()
                        ),
                        'code' => '301',
                    );
                }
                if (array_key_exists('image', $this->templates[$templateId]['data'])) {
                    $data['image'] = $this->getImage($this->templates[$templateId]['data']['image']);
                }

                $data['auth_required'] = false;
                if (BackendModel::isModuleInstalled('Profiles') && $this->frm->getField('auth_required')->isChecked()) {
                    $data['auth_required'] = true;
                    // get all groups and parse them in key value pair
                    $groupItems = BackendProfilesModel::getGroups();
                    // check for groups
                    if (!empty($groupItems)) {
                        $data['auth_groups'] = $this->frm->getField('auth_groups')->getValue();
                    }
                }

                $data['remove_from_search_index'] = false;
                if (BackendModel::isModuleInstalled('Profiles') && $this->frm->getField('remove_from_search_index')->isChecked()  && $this->frm->getField('auth_required')->isChecked()) {
                    $data['remove_from_search_index'] = true;
                }

                // build page record
                $page['id'] = $this->record['id'];
                $page['user_id'] = BackendAuthentication::getUser()->getUserId();
                $page['parent_id'] = $this->record['parent_id'];
                $page['template_id'] = (int) $this->frm->getField('template_id')->getValue();
                $page['meta_id'] = (int) $this->meta->save();
                $page['language'] = BL::getWorkingLanguage();
                $page['type'] = $this->record['type'];
                $page['title'] = $this->frm->getField('title')->getValue();
                $page['navigation_title'] = ($this->frm->getField('navigation_title')->getValue() != '') ? $this->frm->getField('navigation_title')->getValue() : $this->frm->getField('title')->getValue();
                $page['navigation_title_overwrite'] = $this->frm->getField('navigation_title_overwrite')->getActualValue();
                $page['hidden'] = $this->frm->getField('hidden')->getValue();
                $page['status'] = $status;
                $page['publish_on'] = BackendModel::getUTCDate(null, $this->record['publish_on']);
                $page['created_on'] = BackendModel::getUTCDate(null, $this->record['created_on']);
                $page['edited_on'] = BackendModel::getUTCDate();
                $page['allow_move'] = $this->record['allow_move'];
                $page['allow_children'] = $this->record['allow_children'];
                $page['allow_edit'] = $this->record['allow_edit'];
                $page['allow_delete'] = $this->record['allow_delete'];
                $page['sequence'] = $this->record['sequence'];
                $page['data'] = ($data !== null) ? serialize($data) : null;

                if ($this->isGod) {
                    $page['allow_move'] = (in_array(
                        'move',
                        (array) $this->frm->getField('allow')->getValue()
                    )) ? 'Y' : 'N';
                    $page['allow_children'] = (in_array(
                        'children',
                        (array) $this->frm->getField('allow')->getValue()
                    )) ? 'Y' : 'N';
                    $page['allow_edit'] = (in_array(
                        'edit',
                        (array) $this->frm->getField('allow')->getValue()
                    )) ? 'Y' : 'N';
                    $page['allow_delete'] = (in_array(
                        'delete',
                        (array) $this->frm->getField('allow')->getValue()
                    )) ? 'Y' : 'N';
                }

                // set navigation title
                if ($page['navigation_title'] == '') {
                    $page['navigation_title'] = $page['title'];
                }

                // insert page, store the id, we need it when building the blocks
                $page['revision_id'] = BackendPagesModel::update($page);

                // loop blocks
                foreach ($this->blocksContent as $i => $block) {
                    // add page revision id to blocks
                    $this->blocksContent[$i]['revision_id'] = $page['revision_id'];

                    // validate blocks, only save blocks for valid positions
                    if (!in_array(
                        $block['position'],
                        $this->templates[$this->frm->getField('template_id')->getValue()]['data']['names']
                    )
                    ) {
                        unset($this->blocksContent[$i]);
                    }
                }

                // insert the blocks
                BackendPagesModel::insertBlocks($this->blocksContent);

                if ($this->showTags()) {
                    // save tags
                    BackendTagsModel::saveTags(
                        $page['id'],
                        $this->frm->getField('tags')->getValue(),
                        $this->URL->getModule()
                    );
                }

                // build cache
                BackendPagesModel::buildCache(BL::getWorkingLanguage());

                // active
                if ($page['status'] == 'active') {
                    // init var
                    $text = '';

                    // build search-text
                    foreach ($this->blocksContent as $block) {
                        $text .= ' ' . $block['html'];
                    }

                    // add to search index, only if authentication is false
                    if ($data['remove_from_search_index'] == false) {
                        BackendSearchModel::saveIndex(
                            $this->getModule(),
                            $page['id'],
                            array('title' => $page['title'], 'text' => $text)
                        );
                    } else {
                        BackendSearchModel::removeIndex(
                            $this->getModule(),
                            $page['id']
                        );
                    }

                    // everything is saved, so redirect to the overview
                    $this->redirect(
                        BackendModel::createURLForAction(
                            'Edit'
                        ) . '&id=' . $page['id'] . '&report=edited&var=' . rawurlencode(
                            $page['title']
                        ) . '&highlight=row-' . $page['id']
                    );
                } elseif ($page['status'] == 'draft') {
                    // everything is saved, so redirect to the edit action
                    $this->redirect(
                        BackendModel::createURLForAction(
                            'Edit'
                        ) . '&id=' . $page['id'] . '&report=saved-as-draft&var=' . rawurlencode(
                            $page['title']
                        ) . '&highlight=row-' . $page['id'] . '&draft=' . $page['revision_id']
                    );
                }
            }
        }
    }

    /**
     * @param bool $allowImage
     * @return string|null
     */
    private function getImage($allowImage)
    {
        $imageFilename = array_key_exists('image', (array) $this->record['data']) ? $this->record['data']['image'] : null;

        if (!$this->frm->getField('image')->isFilled() && !$this->frm->getField('remove_image')->isChecked()) {
            return $imageFilename;
        }

        $imagePath = FRONTEND_FILES_PATH . '/pages/images';

        // delete the current image
        BackendModel::deleteThumbnails($imagePath, (string) $imageFilename);

        if (!$allowImage
            || ($this->frm->getField('remove_image')->isChecked() && !$this->frm->getField('image')->isFilled())
        ) {
            return null;
        }

        $imageFilename = $this->meta->getURL() . '_' . time() . '.' . $this->frm->getField('image')->getExtension();
        $this->frm->getField('image')->generateThumbnails($imagePath, $imageFilename);

        return $imageFilename;
    }

    /**
     * Check if the user has the right to see/edit tags
     *
     * @return bool
     */
    private function showTags()
    {
        return Authentication::isAllowedAction('Edit', 'Tags') && Authentication::isAllowedAction('GetAllTags', 'Tags');
    }
}
