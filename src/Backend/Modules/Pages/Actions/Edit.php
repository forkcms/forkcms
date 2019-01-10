<?php

namespace Backend\Modules\Pages\Actions;

use Backend\Core\Engine\Authentication;
use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\DataGridDatabase as BackendDataGridDatabase;
use Backend\Core\Engine\DataGridFunctions as BackendDataGridFunctions;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Meta as BackendMeta;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Form\Type\DeleteType;
use Backend\Modules\Extensions\Engine\Model as BackendExtensionsModel;
use Backend\Modules\Pages\Engine\Model as BackendPagesModel;
use Backend\Modules\Search\Engine\Model as BackendSearchModel;
use Backend\Modules\Tags\Engine\Model as BackendTagsModel;
use Backend\Modules\Profiles\Engine\Model as BackendProfilesModel;
use SpoonFormHidden;

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
    private $blocksContent = [];

    /**
     * DataGrid for the drafts
     *
     * @var BackendDataGridDatabase
     */
    private $dgDrafts;

    /**
     * The extras
     *
     * @var array
     */
    private $extras = [];

    /**
     * The hreflang fields
     *
     * @var array
     */
    private $hreflangFields = [];

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
    private $positions = [];

    /**
     * The template data
     *
     * @var array
     */
    private $templates = [];

    public function execute(): void
    {
        parent::execute();

        // load record
        $this->loadData();

        // add js
        $this->header->addJS('jstree/jquery.tree.js', null, false);
        $this->header->addJS('jstree/lib/jquery.cookie.js', null, false);
        $this->header->addJS('jstree/plugins/jquery.tree.cookie.js', null, false);
        $this->header->addJS('/js/vendors/SimpleAjaxUploader.min.js', 'Core', false, true);

        // get the templates
        $this->templates = BackendExtensionsModel::getTemplates();

        // set the default template as checked
        $this->templates[$this->record['template_id']]['checked'] = true;

        // homepage?
        if ($this->id == BackendModel::HOME_PAGE_ID) {
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
        $this->loadDeleteForm();
        $this->parse();
        $this->display();
    }

    private function loadData(): void
    {
        // get record
        $this->id = $this->getRequest()->query->getInt('id');
        $this->isGod = BackendAuthentication::getUser()->isGod();

        // check if something went wrong
        if ($this->id === 0 || !BackendPagesModel::exists($this->id)) {
            $this->redirect(
                BackendModel::createUrlForAction('Index') . '&error=non-existing'
            );
        }

        // get the record
        $this->record = BackendPagesModel::get($this->id);

        // load blocks
        $this->blocksContent = BackendPagesModel::getBlocks($this->id, $this->record['revision_id']);

        // is there a revision specified?
        $revisionToLoad = $this->getRequest()->query->getInt('revision');

        // if this is a valid revision
        if ($revisionToLoad !== 0) {
            // overwrite the current record
            $this->record = (array) BackendPagesModel::get($this->id, $revisionToLoad);

            // load blocks
            $this->blocksContent = BackendPagesModel::getBlocks($this->id, $revisionToLoad);

            // show warning
            $this->template->assign('appendRevision', true);
        }

        // is there a revision specified?
        $draftToLoad = $this->getRequest()->query->getInt('draft');

        // if this is a valid revision
        if ($draftToLoad !== 0) {
            // overwrite the current record
            $this->record = (array) BackendPagesModel::get($this->id, $draftToLoad);

            // load blocks
            $this->blocksContent = BackendPagesModel::getBlocks($this->id, $draftToLoad);

            // show warning
            $this->template->assign('appendRevision', true);
        }

        // reset some vars
        $this->record['full_url'] = BackendPagesModel::getFullUrl($this->record['id']);
    }

    private function loadDrafts(): void
    {
        // create datagrid
        $this->dgDrafts = new BackendDataGridDatabase(
            BackendPagesModel::QUERY_DATAGRID_BROWSE_SPECIFIC_DRAFTS,
            [$this->record['id'], 'draft', BL::getWorkingLanguage()]
        );

        // hide columns
        $this->dgDrafts->setColumnsHidden(['id', 'revision_id']);

        // disable paging
        $this->dgDrafts->setPaging(false);

        // set headers
        $this->dgDrafts->setHeaderLabels(
            [
                 'user_id' => \SpoonFilter::ucfirst(BL::lbl('By')),
                 'edited_on' => \SpoonFilter::ucfirst(BL::lbl('LastEditedOn')),
            ]
        );

        // set column-functions
        $this->dgDrafts->setColumnFunction([new BackendDataGridFunctions(), 'getUser'], ['[user_id]'], 'user_id');
        $this->dgDrafts->setColumnFunction(
            [new BackendDataGridFunctions(), 'getTimeAgo'],
            ['[edited_on]'],
            'edited_on'
        );

        // our JS needs to know an id, so we can highlight it
        $this->dgDrafts->setRowAttributes(['id' => 'row-[revision_id]']);

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('Edit')) {
            // set column URLs
            $this->dgDrafts->setColumnURL(
                'title',
                BackendModel::createUrlForAction('Edit') . '&amp;id=[id]&amp;draft=[revision_id]'
            );

            // add use column
            $this->dgDrafts->addColumn(
                'use_draft',
                null,
                BL::lbl('UseThisDraft'),
                BackendModel::createUrlForAction('Edit') . '&amp;id=[id]&amp;draft=[revision_id]',
                BL::lbl('UseThisDraft')
            );
        }
    }

    private function loadForm(): void
    {
        // get default template id
        $defaultTemplateId = $this->get('fork.settings')->get('Pages', 'default_template', 1);

        // create form
        $this->form = new BackendForm('edit');

        // assign in template
        $this->template->assign('defaultTemplateId', $defaultTemplateId);

        // create elements
        $this->form->addText('title', $this->record['title'], null, 'form-control title', 'form-control danger title');
        $this->form->addEditor('html');
        $this->form->addHidden('template_id', $this->record['template_id']);
        $this->form->addRadiobutton(
            'hidden',
            [
                 ['label' => BL::lbl('Hidden'), 'value' => 1],
                 ['label' => BL::lbl('Published'), 'value' => 0],
            ],
            $this->record['hidden']
        );

        // image related fields
        $this->form->addImage('image')->setAttribute('data-fork-cms-role', 'image-field');
        $this->form->addCheckbox('remove_image');

        // move page fields
        $chkMovePage = $this->form->addCheckbox('move_page');
        $chkMovePage->setAttribute('data-role', 'move-page-toggle');
        if (!(bool) $this->record['allow_move']) {
            $chkMovePage->setAttribute('disabled');
            $chkMovePage->setAttribute('class', 'fork-form-checkbox disabled');
        }

        $movePageTreeOptions = [
            'main' => BL::lbl('MainNavigation'),
            'meta' => BL::lbl('Meta'),
            'footer' => BL::lbl('Footer'),
            'root' => BL::lbl('Root'),
        ];
        if (!BackendModel::get('fork.settings')->get('Pages', 'meta_navigation', false)) {
            unset($movePageTreeOptions['meta']);
        }
        $this->form->addDropdown(
            'move_page_tree',
            $movePageTreeOptions,
            BackendPagesModel::getTreeNameForPageId($this->id)
        )->setAttribute('data-role', 'move-page-tree-changer');

        $this->form->addDropdown(
            'move_page_type',
            [
                BackendPagesModel::TYPE_OF_DROP_BEFORE => BL::lbl('BeforePage'),
                BackendPagesModel::TYPE_OF_DROP_AFTER  => BL::lbl('AfterPage'),
                BackendPagesModel::TYPE_OF_DROP_INSIDE  => BL::lbl('InsidePage')
            ],
            BackendPagesModel::TYPE_OF_DROP_INSIDE
        )->setAttribute('data-role', 'move-page-type-changer');
        $dropdownPageTree = BackendPagesModel::getMoveTreeForDropdown($this->id);

        $ddmMovePageReferencePage = $this->form->addDropdown(
            'move_page_reference_page',
            (array) $dropdownPageTree['pages'],
            $this->record['parent_id'] != 0 ? $this->record['parent_id'] : null
        )->setDefaultElement(BL::lbl('AppendToTree'), 0)->setAttribute('data-role', 'move-page-pages-select');
        foreach ((array) $dropdownPageTree['attributes'] as $value => $attributes) {
            $ddmMovePageReferencePage->setOptionAttributes($value, $attributes);
        }

        // just execute if the site is multi-language
        if ($this->getContainer()->getParameter('site.multilanguage')) {
            // loop active languages
            foreach (BL::getActiveLanguages() as $language) {
                if ($language != BL::getWorkingLanguage()) {
                    $pages = BackendPagesModel::getPagesForDropdown($language);
                    // add field for each language
                    $field = $this->form->addDropdown('hreflang_' . $language, $pages, (!empty($this->record['data']['hreflang_' . $language]) ? $this->record['data']['hreflang_' . $language] : null))->setDefaultElement('');
                    $this->hreflangFields[$language]['field_hreflang'] = $field->parse();
                }
            }
        }

        // page auth related fields
        // check if profiles module is installed
        if (BackendModel::isModuleInstalled('Profiles')) {
            // add checkbox for auth_required
            $this->form->addCheckbox(
                'auth_required',
                isset($this->record['data']['auth_required']) && $this->record['data']['auth_required']
            );

            // add checkbox for index page to search
            $this->form->addCheckbox(
                'remove_from_search_index',
                isset($this->record['data']['remove_from_search_index']) && $this->record['data']['remove_from_search_index']
            );

            // get all groups and parse them in key value pair
            $groupItems = BackendProfilesModel::getGroups();
            if (!empty($groupItems)) {
                $groups = [];
                foreach ($groupItems as $key => $item) {
                    $groups[] = ['label' => $item, 'value' => $key];
                }
                // set checked values
                $checkedGroups = [];
                if (isset($this->record['data']['auth_groups']) && is_array($this->record['data']['auth_groups'])) {
                    foreach ($this->record['data']['auth_groups'] as $group) {
                        $checkedGroups[] = $group;
                    }
                }
                // add multi checkbox
                $this->form->addMultiCheckbox('auth_groups', $groups, $checkedGroups);
            }
        }

        // a god user should be able to adjust the detailed settings for a page easily
        if ($this->isGod) {
            $permissions = [
                'move' => ['data-role' => 'allow-move-toggle'],
                'children' => ['data-role' => 'allow-children-toggle'],
                'edit' => ['data-role' => 'allow-edit-toggle'],
                'delete' => ['data-role' => 'allow-delete-toggle'],
            ];
            if (BackendPagesModel::isForbiddenToMove($this->id)) {
                $permissions['move']['disabled'] = null;
            }
            if (BackendPagesModel::isForbiddenToHaveChildren($this->id)) {
                $permissions['children']['disabled'] = null;
            }
            if (BackendPagesModel::isForbiddenToDelete($this->id)) {
                $permissions['delete']['disabled'] = null;
            }
            $checked = [];
            $values = [];

            foreach ($permissions as $permission => $attributes) {
                $values[] = [
                    'label' => BL::msg(\SpoonFilter::toCamelCase('allow_' . $permission)),
                    'value' => $permission,
                    'attributes' => $attributes,
                ];
                if (isset($this->record['allow_' . $permission]) && $this->record['allow_' . $permission]) {
                    $checked[] = $permission;
                }
            }

            $this->form->addMultiCheckbox('allow', $values, $checked);
        }

        // build prototype block
        $block = [];
        $block['index'] = 0;
        $block['formElements']['chkVisible'] = $this->form->addCheckbox('block_visible_' . $block['index'], true);
        $block['formElements']['hidExtraId'] = $this->form->addHidden('block_extra_id_' . $block['index'], 0);
        $block['formElements']['hidExtraType'] = $this->form->addHidden('block_extra_type_' . $block['index'], 'rich_text');
        $block['formElements']['hidExtraData'] = $this->form->addHidden('block_extra_data_' . $block['index']);
        $block['formElements']['hidPosition'] = $this->form->addHidden('block_position_' . $block['index'], 'fallback');
        $block['formElements']['txtHTML'] = $this->form->addTextarea(
            'block_html_' . $block['index'],
            ''
        ); // this is no editor; we'll add the editor in JS

        // add default block to "fallback" position, the only one which we can rest assured to exist
        $this->positions['fallback']['blocks'][] = $block;

        // content has been submitted: re-create submitted content rather than the database-fetched content
        if ($this->getRequest()->request->has('block_html_0')) {
            $this->blocksContent = [];
            $hasBlock = false;
            $i = 1;

            // loop submitted blocks
            $positions = [];
            while ($this->getRequest()->request->has('block_position_' . $i)) {
                $block = [];

                // save block position
                $block['position'] = $this->getRequest()->request->get('block_position_' . $i);
                $positions[$block['position']][] = $block;

                // set linked extra
                $block['extra_id'] = $this->getRequest()->request->get('block_extra_id_' . $i);
                $block['extra_type'] = $this->getRequest()->request->get('block_extra_type_' . $i);
                $block['extra_data'] = $this->getRequest()->request->get('block_extra_data_' . $i);

                // reset some stuff
                if ($block['extra_id'] <= 0) {
                    $block['extra_id'] = null;
                }

                // init html
                $block['html'] = null;

                $html = $this->getRequest()->request->get('block_html_' . $i);

                // extra-type is HTML
                if ($block['extra_id'] === null || $block['extra_type'] == 'usertemplate') {
                    if ($this->getRequest()->request->get('block_extra_type_' . $i) === 'usertemplate') {
                        $block['extra_id'] = $this->getRequest()->request->get('block_extra_id_' . $i);
                        $_POST['block_extra_data_' . $i] = htmlspecialchars($_POST['block_extra_data_' . $i]);
                    } else {
                        // reset vars
                        $block['extra_id'] = null;
                    }
                    $block['html'] = $html;
                } else {
                    // type of block
                    if (isset($this->extras[$block['extra_id']]['type']) && $this->extras[$block['extra_id']]['type'] == 'block') {
                        // set error
                        if ($hasBlock) {
                            $this->form->addError(BL::err('CantAdd2Blocks'));
                        }

                        // home can't have blocks
                        if ($this->record['id'] == BackendModel::HOME_PAGE_ID) {
                            $this->form->addError(BL::err('HomeCantHaveBlocks'));
                        }

                        // reset var
                        $hasBlock = true;
                    }
                }

                // set data
                $block['created_on'] = BackendModel::getUTCDate();
                $block['edited_on'] = $block['created_on'];
                $block['visible'] = $this->getRequest()->request->getBoolean('block_visible_' . $i);
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
            $block['formElements']['chkVisible'] = $this->form->addCheckbox(
                'block_visible_' . $block['index'],
                $block['visible']
            );
            $block['formElements']['hidExtraId'] = $this->form->addHidden(
                'block_extra_id_' . $block['index'],
                (int) $block['extra_id']
            );
            $block['formElements']['hidExtraType'] = $this->form->addHidden(
                'block_extra_type_' . $block['index'],
                $block['extra_type']
            );
            $this->form->add(
                $this->getHiddenJsonField(
                    'block_extra_data_' . $block['index'],
                    $block['extra_data']
                )
            );
            $block['formElements']['hidExtraData'] = $this->form->getField('block_extra_data_' . $block['index']);
            $block['formElements']['hidPosition'] = $this->form->addHidden(
                'block_position_' . $block['index'],
                $block['position']
            );
            $block['formElements']['txtHTML'] = $this->form->addTextarea(
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
        $redirectValues = [
            ['value' => 'none', 'label' => \SpoonFilter::ucfirst(BL::lbl('None'))],
            [
                'value' => 'internal',
                'label' => \SpoonFilter::ucfirst(BL::lbl('InternalLink')),
                'variables' => ['isInternal' => true],
            ],
            [
                'value' => 'external',
                'label' => \SpoonFilter::ucfirst(BL::lbl('ExternalLink')),
                'variables' => ['isExternal' => true],
            ],
        ];
        $this->form->addRadiobutton('redirect', $redirectValues, $redirectValue);
        $this->form->addDropdown(
            'internal_redirect',
            BackendPagesModel::getPagesForDropdown(),
            ($redirectValue == 'internal') ? $this->record['data']['internal_redirect']['page_id'] : null
        );
        $this->form->addText(
            'external_redirect',
            ($redirectValue == 'external') ? urldecode($this->record['data']['external_redirect']['url']) : null,
            null,
            null,
            null,
            true
        );

        // page info
        $this->form->addCheckbox('navigation_title_overwrite', $this->record['navigation_title_overwrite']);
        $this->form->addText('navigation_title', $this->record['navigation_title']);

        if ($this->userCanSeeAndEditTags()) {
            // tags
            $this->form->addText(
                'tags',
                BackendTagsModel::getTags($this->url->getModule(), $this->id),
                null,
                'form-control js-tags-input',
                'error js-tags-input'
            )->setAttribute('aria-describedby', 'tags-info');
        }

        // a specific action
        $isAction = isset($this->record['data']['is_action']) && $this->record['data']['is_action'];
        $this->form->addCheckbox('is_action', $isAction);

        // extra
        $blockTypes = BackendPagesModel::getTypes();
        $this->form->addDropdown('extra_type', $blockTypes, key($blockTypes));

        // meta
        $this->meta = new BackendMeta($this->form, $this->record['meta_id'], 'title', true);

        // set callback for generating an unique URL
        $this->meta->setUrlCallback(
            'Backend\Modules\Pages\Engine\Model',
            'getUrl',
            [$this->record['id'], $this->record['parent_id'], $isAction]
        );
    }

    private function loadRevisions(): void
    {
        // create datagrid
        $this->dgRevisions = new BackendDataGridDatabase(
            BackendPagesModel::QUERY_BROWSE_REVISIONS,
            [
                 $this->id,
                 'archive',
                 BL::getWorkingLanguage(
                 ),
            ]
        );

        // hide columns
        $this->dgRevisions->setColumnsHidden(['id', 'revision_id']);

        // disable paging
        $this->dgRevisions->setPaging(false);

        // set headers
        $this->dgRevisions->setHeaderLabels(
            [
                 'user_id' => \SpoonFilter::ucfirst(BL::lbl('By')),
                 'edited_on' => \SpoonFilter::ucfirst(BL::lbl('LastEditedOn')),
            ]
        );

        // set functions
        $this->dgRevisions->setColumnFunction(
            [new BackendDataGridFunctions(), 'getUser'],
            ['[user_id]'],
            'user_id'
        );
        $this->dgRevisions->setColumnFunction(
            [new BackendDataGridFunctions(), 'getTimeAgo'],
            ['[edited_on]'],
            'edited_on'
        );

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('Edit')) {
            // set column URLs
            $this->dgRevisions->setColumnURL(
                'title',
                BackendModel::createUrlForAction('Edit') . '&amp;id=[id]&amp;revision=[revision_id]'
            );

            // add use column
            $this->dgRevisions->addColumn(
                'use_revision',
                null,
                BL::lbl('UseThisVersion'),
                BackendModel::createUrlForAction('Edit') . '&amp;id=[id]&amp;revision=[revision_id]',
                BL::lbl('UseThisVersion')
            );
        }
    }

    protected function parse(): void
    {
        parent::parse();

        // set
        $this->record['url'] = $this->meta->getUrl();
        if ($this->id == 1) {
            $this->record['url'] = '';
        }

        // parse some variables
        $this->template->assign('item', $this->record);
        $this->template->assign('isGod', $this->isGod);
        $this->template->assign('templates', $this->templates);
        $this->template->assign('positions', $this->positions);
        $this->template->assign('extrasData', json_encode(BackendExtensionsModel::getExtrasData()));
        $this->template->assign('extrasById', json_encode($this->extras));
        $this->template->assign('prefixURL', rtrim(BackendPagesModel::getFullUrl($this->record['parent_id']), '/'));
        $this->template->assign('formErrors', (string) $this->form->getErrors());
        $this->template->assign('showTags', $this->userCanSeeAndEditTags());
        $this->template->assign('hreflangFields', $this->hreflangFields);
        $this->header->appendDetailToBreadcrumbs($this->record['title']);

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
        $this->template->assign('allowPagesDelete', $showDelete);

        // assign template
        $this->template->assignArray($this->templates[$this->record['template_id']], 'template');

        // parse datagrids
        $this->template->assign(
            'revisions',
            ($this->dgRevisions->getNumResults() != 0) ? $this->dgRevisions->getContent() : false
        );
        $this->template->assign('drafts', ($this->dgDrafts->getNumResults() != 0) ? $this->dgDrafts->getContent() : false);

        // parse the tree
        $this->template->assign('tree', BackendPagesModel::getTreeHTML());

        // assign if profiles module is installed
        $this->template->assign('showAuthenticationTab', BackendModel::isModuleInstalled('Profiles'));

        $this->header->addJsData(
            'pages',
            'userTemplates',
            BackendPagesModel::loadUserTemplates()
        );
    }

    private function validateForm(): void
    {
        if (!$this->form->isSubmitted()) {
            return;
        }

        $status = $this->getRequest()->request->get('status');
        if (!in_array($status, ['active', 'draft'], true)) {
            $status = 'active';
        }

        $redirectValue = $this->form->getField('redirect')->getValue();
        if ($redirectValue === 'internal') {
            $this->form->getField('internal_redirect')->isFilled(
                BL::err('FieldIsRequired')
            );
        }
        if ($redirectValue === 'external') {
            $this->form->getField('external_redirect')->isURL(BL::err('InvalidURL'));
        }

        // set callback for generating an unique URL
        $this->meta->setUrlCallback(
            BackendPagesModel::class,
            'getUrl',
            [$this->record['id'], $this->record['parent_id'], $this->form->getField('is_action')->getChecked()]
        );

        $this->form->cleanupFields();
        $this->form->getField('title')->isFilled(BL::err('TitleIsRequired'));
        $this->meta->validate();

        if ($this->form->getField('move_page')->isChecked()) {
            $this->form->getField('move_page_tree')->isFilled(BL::err('FieldIsRequired'));
            $this->form->getField('move_page_type')->isFilled(BL::err('FieldIsRequired'));
            $this->form->getField('move_page_reference_page')->isFilled(BL::err('FieldIsRequired'));
        }

        if (!$this->form->isCorrect()) {
            return;
        }

        $data = $this->buildPageData($redirectValue);

        $page = [
            'id' => $this->record['id'],
            'user_id' => BackendAuthentication::getUser()->getUserId(),
            'parent_id' => $this->record['parent_id'],
            'template_id' => (int) $this->form->getField('template_id')->getValue(),
            'meta_id' => $this->meta->save(),
            'language' => BL::getWorkingLanguage(),
            'type' => $this->record['type'],
            'title' => $this->form->getField('title')->getValue(),
            'navigation_title' => !empty($this->form->getField('navigation_title')->getValue())
                ? $this->form->getField('navigation_title')->getValue() : $this->form->getField('title')->getValue(),
            'navigation_title_overwrite' => $this->form->getField('navigation_title_overwrite')->isChecked(),
            'hidden' => $this->form->getField('hidden')->getValue(),
            'status' => $status,
            'publish_on' => BackendModel::getUTCDate(null, $this->record['publish_on']),
            'created_on' => BackendModel::getUTCDate(null, $this->record['created_on']),
            'edited_on' => BackendModel::getUTCDate(),
            'allow_move' => $this->record['allow_move'],
            'allow_children' => $this->record['allow_children'],
            'allow_edit' => $this->record['allow_edit'],
            'allow_delete' => $this->record['allow_delete'],
            'sequence' => $this->record['sequence'],
            'data' => serialize($data),
        ];

        $page = $this->changePagePermissions($page);

        if (empty($page['navigation_title'])) {
            $page['navigation_title'] = $page['title'];
        }

        // update page, store the revision id, we need it when building the blocks
        $page['revision_id'] = BackendPagesModel::update($page);

        $this->insertBlocks($page['revision_id']);

        $this->saveTags($page['id']);

        $cacheShouldBeUpdated = !(
            $this->record['title'] === $page['title']
            && $this->record['navigation_title'] === $page['navigation_title']
            && $this->record['navigation_title_overwrite'] === $page['navigation_title_overwrite']
            && $this->record['hidden'] === $page['hidden']
        );

        // build cache
        if ($cacheShouldBeUpdated) {
            BackendPagesModel::buildCache(BL::getWorkingLanguage());
        }

        if ($page['status'] === 'draft') {
            $this->redirect(
                BackendModel::createUrlForAction('Edit') . '&id=' . $page['id']
                . '&report=saved-as-draft&var=' . rawurlencode($page['title']) . '&highlight=row-' . $page['id']
                . '&draft=' . $page['revision_id']
            );

            return;
        }

        $this->movePage($page);
        $this->saveSearchIndex($data['remove_from_search_index'] || $redirectValue !== 'none', $page);

        $this->redirect(
            BackendModel::createUrlForAction('Edit') . '&id=' . $page['id'] . '&report=edited&var='
            . rawurlencode($page['title']) . '&highlight=row-' . $page['id']
        );
    }

    private function changePagePermissions(array $page): array
    {
        if (!$this->isGod) {
            return $page;
        }

        $page['allow_move'] = BackendPagesModel::isForbiddenToMove($this->id) ? false : in_array(
            'move',
            (array) $this->form->getField('allow')->getValue(),
            true
        );
        $page['allow_children'] = BackendPagesModel::isForbiddenToHaveChildren($this->id) ? false : in_array(
            'children',
            (array) $this->form->getField('allow')->getValue(),
            true
        );
        $page['allow_edit'] = in_array(
            'edit',
            (array) $this->form->getField('allow')->getValue(),
            true
        );
        $page['allow_delete'] = BackendPagesModel::isForbiddenToDelete($this->id) ? false : in_array(
            'delete',
            (array) $this->form->getField('allow')->getValue(),
            true
        );

        return $page;
    }

    private function movePage(array $page): void
    {
        if (!$page['allow_move'] || !$this->form->getField('move_page')->isChecked()) {
            return;
        }

        BackendPagesModel::move(
            $page['id'],
            (int) $this->form->getField('move_page_reference_page')->getValue(),
            $this->form->getField('move_page_type')->getValue(),
            $this->form->getField('move_page_tree')->getValue()
        );
        BackendPagesModel::buildCache(BL::getWorkingLanguage());
    }

    private function buildPageData(string $redirectValue): array
    {
        $data = [];
        $templateId = (int) $this->form->getField('template_id')->getValue();

        if ($this->form->getField('is_action')->isChecked()) {
            $data['is_action'] = true;
        }
        if ($redirectValue === 'internal') {
            $data['internal_redirect'] = [
                'page_id' => $this->form->getField('internal_redirect')->getValue(),
                'code' => '301',
            ];
        }
        if ($redirectValue === 'external') {
            $data['external_redirect'] = [
                'url' => BackendPagesModel::getEncodedRedirectUrl(
                    $this->form->getField('external_redirect')->getValue()
                ),
                'code' => '301',
            ];
        }
        if (array_key_exists('image', $this->templates[$templateId]['data'])) {
            $data['image'] = $this->getImage($this->templates[$templateId]['data']['image']);
        }

        $data['auth_required'] = false;
        if (BackendModel::isModuleInstalled('Profiles') && $this->form->getField('auth_required')->isChecked()) {
            $data['auth_required'] = true;
            // get all groups and parse them in key value pair
            $groupItems = BackendProfilesModel::getGroups();

            if (!empty($groupItems)) {
                $data['auth_groups'] = $this->form->getField('auth_groups')->getValue();
            }
        }

        $data['remove_from_search_index'] = false;
        if (BackendModel::isModuleInstalled('Profiles')
            && $this->form->getField('remove_from_search_index')->isChecked()
            && $this->form->getField('auth_required')->isChecked()) {
            $data['remove_from_search_index'] = true;
        }

        // just execute if the site is multi-language
        if ($this->getContainer()->getParameter('site.multilanguage')) {
            // loop active languages
            foreach (BL::getActiveLanguages() as $language) {
                if ($language != BL::getWorkingLanguage()
                    && $this->form->getfield('hreflang_' . $language)->isFilled()) {
                    $data['hreflang_' . $language] = $this->form->getfield('hreflang_' . $language)->getValue();
                }
            }
        }

        return $data;
    }

    private function insertBlocks(int $revisionId): void
    {
        $possiblePositions = $this->templates[$this->form->getField('template_id')->getValue()]['data']['names'];
        foreach ($this->blocksContent as $i => $block) {
            $this->blocksContent[$i]['revision_id'] = $revisionId;

            // validate blocks, only save blocks for valid positions
            if (!in_array($block['position'], $possiblePositions, true)) {
                unset($this->blocksContent[$i]);
            }
        }

        BackendPagesModel::insertBlocks($this->blocksContent);
    }

    private function saveTags(int $pageId): void
    {
        if (!$this->userCanSeeAndEditTags()) {
            return;
        }

        BackendTagsModel::saveTags(
            $pageId,
            $this->form->getField('tags')->getValue(),
            $this->url->getModule()
        );
    }

    private function saveSearchIndex(bool $removeFromSearchIndex, array $page): void
    {
        if ($removeFromSearchIndex) {
            BackendSearchModel::removeIndex(
                $this->getModule(),
                $page['id']
            );

            return;
        }

        $searchText = '';
        foreach ($this->blocksContent as $block) {
            $searchText .= ' ' . $block['html'];
        }

        BackendSearchModel::saveIndex(
            $this->getModule(),
            $page['id'],
            ['title' => $page['title'], 'text' => $searchText]
        );
    }

    private function getImage(bool $allowImage): ?string
    {
        $imageFilename = array_key_exists('image', (array) $this->record['data']) ? $this->record['data']['image'] : null;

        if (!$this->form->getField('image')->isFilled() && !$this->form->getField('remove_image')->isChecked()) {
            return $imageFilename;
        }

        $imagePath = FRONTEND_FILES_PATH . '/Pages/images';

        // delete the current image
        BackendModel::deleteThumbnails($imagePath, (string) $imageFilename);

        if (!$allowImage
            || ($this->form->getField('remove_image')->isChecked() && !$this->form->getField('image')->isFilled())
        ) {
            return null;
        }

        $imageFilename = $this->meta->getUrl() . '_' . time() . '.' . $this->form->getField('image')->getExtension();
        $this->form->getField('image')->generateThumbnails($imagePath, $imageFilename);

        return $imageFilename;
    }

    private function userCanSeeAndEditTags(): bool
    {
        return Authentication::isAllowedAction('Edit', 'Tags') && Authentication::isAllowedAction('GetAllTags', 'Tags');
    }

    private function loadDeleteForm(): void
    {
        $deleteForm = $this->createForm(
            DeleteType::class,
            ['id' => $this->record['id']],
            ['module' => $this->getModule()]
        );
        $this->template->assign('deleteForm', $deleteForm->createView());
    }

    private function getHiddenJsonField(string $name, ?string $json): SpoonFormHidden
    {
        return new class($name, htmlspecialchars($json)) extends SpoonFormHidden {
            public function getValue($allowHTML = null)
            {
                return parent::getValue(true);
            }
        };
    }
}
