<?php

namespace Backend\Modules\Pages\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Authentication;
use Backend\Core\Engine\Base\ActionAdd as BackendBaseActionAdd;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Meta as BackendMeta;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Extensions\Engine\Model as BackendExtensionsModel;
use Backend\Modules\Pages\Engine\Model as BackendPagesModel;
use Backend\Modules\Search\Engine\Model as BackendSearchModel;
use Backend\Modules\Tags\Engine\Model as BackendTagsModel;
use SpoonFormHidden;

/**
 * This is the add-action, it will display a form to create a new item
 */
class Add extends BackendBaseActionAdd
{
    /**
     * The blocks linked to this page
     *
     * @var array
     */
    private $blocksContent = [];

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
     * The extras
     *
     * @var array
     */
    private $extras = [];

    /**
     * The template data
     *
     * @var array
     */
    private $templates = [];

    public function execute(): void
    {
        // call parent, this will probably add some general CSS/JS or other required files
        parent::execute();

        // add js
        $this->header->addJS('jstree/jquery.tree.js', null, false);
        $this->header->addJS('jstree/lib/jquery.cookie.js', null, false);
        $this->header->addJS('jstree/plugins/jquery.tree.cookie.js', null, false);
        $this->header->addJS('/js/vendors/SimpleAjaxUploader.min.js', 'Core', false, true);

        // get the templates
        $this->templates = BackendExtensionsModel::getTemplates();
        $this->isGod = BackendAuthentication::getUser()->isGod();

        // init var
        $defaultTemplateId = $this->get('fork.settings')->get('Pages', 'default_template', false);

        // fallback
        if ($defaultTemplateId === false) {
            // get first key
            $keys = array_keys($this->templates);

            // set the first items as default if no template was set as default.
            $defaultTemplateId = $this->templates[$keys[0]]['id'];
        }

        // set the default template as checked
        $this->templates[$defaultTemplateId]['checked'] = true;

        // get the extras
        $this->extras = BackendExtensionsModel::getExtras();

        $this->loadForm();
        $this->validateForm();
        $this->parse();
        $this->display();
    }

    private function loadForm(): void
    {
        // get default template id
        $defaultTemplateId = $this->get('fork.settings')->get('Pages', 'default_template', 1);

        // create form
        $this->form = new BackendForm('add');

        // assign in template
        $this->template->assign('defaultTemplateId', $defaultTemplateId);

        // create elements
        $this->form->addText('title', null, null, 'form-control title', 'form-control danger title');
        $this->form->addEditor('html');
        $this->form->addHidden('template_id', $defaultTemplateId);
        $this->form->addRadiobutton(
            'hidden',
            [
                ['label' => BL::lbl('Hidden'), 'value' => 1],
                ['label' => BL::lbl('Published'), 'value' => 0],
            ],
            0
        );

        // image related fields
        $this->form->addImage('image');

        // a god user should be able to adjust the detailed settings for a page easily
        if ($this->isGod) {
            // init some vars
            $items = [
                'move' => true,
                'children' => true,
                'edit' => true,
                'delete' => true,
            ];
            $checked = [];
            $values = [];

            foreach ($items as $value => $itemIsChecked) {
                $values[] = ['label' => BL::msg(\SpoonFilter::toCamelCase('allow_' . $value)), 'value' => $value];

                if ($itemIsChecked) {
                    $checked[] = $value;
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
            'block_html_' . $block['index']
        ); // this is no editor; we'll add the editor in JS

        // add default block to "fallback" position, the only one which we can rest assured to exist
        $this->positions['fallback']['blocks'][] = $block;

        // content has been submitted: re-create submitted content rather than the database-fetched content
        if ($this->getRequest()->request->has('block_html_0')) {
            $this->blocksContent = [];
            $hasBlock = false;
            $i = 1;

            $positions = [];
            // loop submitted blocks
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
        $this->form->addRadiobutton('redirect', $redirectValues, 'none');
        $this->form->addDropdown('internal_redirect', BackendPagesModel::getPagesForDropdown());
        $this->form->addText('external_redirect', null, null, null, null, true);

        // page info
        $this->form->addCheckbox('navigation_title_overwrite');
        $this->form->addText('navigation_title');

        if ($this->showTags()) {
            // tags
            $this->form->addText('tags', null, null, 'form-control js-tags-input', 'form-control danger js-tags-input');
        }

        // a specific action
        $this->form->addCheckbox('is_action', false);

        // extra
        $blockTypes = BackendPagesModel::getTypes();
        $this->form->addDropdown('extra_type', $blockTypes, key($blockTypes));

        // meta
        $this->meta = new BackendMeta($this->form, null, 'title', true);

        // set callback for generating an unique URL
        $this->meta->setUrlCallback(
            'Backend\Modules\Pages\Engine\Model',
            'getUrl',
            [0, $this->getRequest()->query->getInt('parent'), false]
        );
    }

    protected function parse(): void
    {
        parent::parse();

        // parse some variables
        $this->template->assign('templates', $this->templates);
        $this->template->assign('isGod', $this->isGod);
        $this->template->assign('positions', $this->positions);
        $this->template->assign('extrasData', json_encode(BackendExtensionsModel::getExtrasData()));
        $this->template->assign('extrasById', json_encode(BackendExtensionsModel::getExtras()));
        $this->template->assign(
            'prefixURL',
            rtrim(BackendPagesModel::getFullUrl($this->getRequest()->query->getInt('parent', 1)), '/')
        );
        $this->template->assign('formErrors', (string) $this->form->getErrors());
        $this->template->assign('showTags', $this->showTags());

        // get default template id
        $defaultTemplateId = $this->get('fork.settings')->get('Pages', 'default_template', 1);

        // assign template
        $this->template->assignArray($this->templates[$defaultTemplateId], 'template');

        // parse the form
        $this->form->parse($this->template);

        // parse the tree
        $this->template->assign('tree', BackendPagesModel::getTreeHTML());

        $this->header->addJsData(
            'pages',
            'userTemplates',
            BackendPagesModel::loadUserTemplates()
        );
    }

    private function validateForm(): void
    {
        // is the form submitted?
        if ($this->form->isSubmitted()) {
            // get the status
            $status = $this->getRequest()->request->get('status');
            if (!in_array($status, ['active', 'draft'])) {
                $status = 'active';
            }

            // validate redirect
            $redirectValue = $this->form->getField('redirect')->getValue();
            if ($redirectValue == 'internal') {
                $this->form->getField('internal_redirect')->isFilled(
                    BL::err('FieldIsRequired')
                );
            }
            if ($redirectValue == 'external') {
                $this->form->getField('external_redirect')->isURL(BL::err('InvalidURL'));
            }

            // set callback for generating an unique URL
            $this->meta->setUrlCallback(
                'Backend\Modules\Pages\Engine\Model',
                'getUrl',
                [0, $this->getRequest()->query->getInt('parent'), $this->form->getField('is_action')->getChecked()]
            );

            // cleanup the submitted fields, ignore fields that were added by hackers
            $this->form->cleanupFields();

            // validate fields
            $this->form->getField('title')->isFilled(BL::err('TitleIsRequired'));

            // validate meta
            $this->meta->validate();

            // no errors?
            if ($this->form->isCorrect()) {
                // init var
                $parentId = $this->getRequest()->query->getInt('parent');
                $parentPage = BackendPagesModel::get($parentId);
                if (!$parentPage || !$parentPage['children_allowed']) {
                    // no children allowed
                    $parentId = 0;
                    $parentPage = false;
                }
                $templateId = (int) $this->form->getField('template_id')->getValue();
                $data = null;

                // build data
                if ($this->form->getField('is_action')->isChecked()) {
                    $data['is_action'] = true;
                }
                if ($redirectValue == 'internal') {
                    $data['internal_redirect'] = [
                        'page_id' => $this->form->getField('internal_redirect')->getValue(),
                        'code' => '301',
                    ];
                }
                if ($redirectValue == 'external') {
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

                // build page record
                $page = [];
                $page['id'] = BackendPagesModel::getMaximumPageId() + 1;
                $page['user_id'] = BackendAuthentication::getUser()->getUserId();
                $page['parent_id'] = $parentId;
                $page['template_id'] = $templateId;
                $page['meta_id'] = (int) $this->meta->save();
                $page['language'] = BL::getWorkingLanguage();
                $page['type'] = $parentPage ? 'page' : 'root';
                $page['title'] = $this->form->getField('title')->getValue();
                $page['navigation_title'] = ($this->form->getField('navigation_title')->getValue(
                ) != '') ? $this->form->getField('navigation_title')->getValue() : $this->form->getField(
                    'title'
                )->getValue();
                $page['navigation_title_overwrite'] = $this->form->getField(
                    'navigation_title_overwrite'
                )->isChecked();
                $page['hidden'] = $this->form->getField('hidden')->getValue();
                $page['status'] = $status;
                $page['publish_on'] = BackendModel::getUTCDate();
                $page['created_on'] = BackendModel::getUTCDate();
                $page['edited_on'] = BackendModel::getUTCDate();
                $page['allow_move'] = true;
                $page['allow_children'] = true;
                $page['allow_edit'] = true;
                $page['allow_delete'] = true;
                $page['sequence'] = BackendPagesModel::getMaximumSequence($parentId) + 1;
                $page['data'] = ($data !== null) ? serialize($data) : null;

                if ($this->isGod) {
                    $page['allow_move'] = in_array(
                        'move',
                        (array) $this->form->getField('allow')->getValue(),
                        true
                    );
                    $page['allow_children'] = in_array(
                        'children',
                        (array) $this->form->getField('allow')->getValue(),
                        true
                    );
                    $page['allow_edit'] = in_array(
                        'edit',
                        (array) $this->form->getField('allow')->getValue(),
                        true
                    );
                    $page['allow_delete'] = in_array(
                        'delete',
                        (array) $this->form->getField('allow')->getValue(),
                        true
                    );
                }

                // set navigation title
                if ($page['navigation_title'] == '') {
                    $page['navigation_title'] = $page['title'];
                }

                // insert page, store the id, we need it when building the blocks
                $page['revision_id'] = BackendPagesModel::insert($page);

                // loop blocks
                foreach ($this->blocksContent as $i => $block) {
                    // add page revision id to blocks
                    $this->blocksContent[$i]['revision_id'] = $page['revision_id'];

                    // validate blocks, only save blocks for valid positions
                    if (!in_array(
                        $block['position'],
                        $this->templates[$this->form->getField('template_id')->getValue()]['data']['names']
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
                        $this->form->getField('tags')->getValue(),
                        $this->url->getModule()
                    );
                }

                // build the cache
                BackendPagesModel::buildCache(BL::getWorkingLanguage());

                // active
                if ($page['status'] == 'active') {
                    // init var
                    $text = '';

                    // build search-text
                    foreach ($this->blocksContent as $block) {
                        $text .= ' ' . $block['html'];
                    }

                    // add to search index
                    BackendSearchModel::saveIndex(
                        $this->getModule(),
                        $page['id'],
                        ['title' => $page['title'], 'text' => $text]
                    );

                    // everything is saved, so redirect to the overview
                    $this->redirect(
                        BackendModel::createUrlForAction(
                            'Edit'
                        ) . '&id=' . $page['id'] . '&report=added&var=' . rawurlencode(
                            $page['title']
                        ) . '&highlight=row-' . $page['id']
                    );
                } elseif ($page['status'] == 'draft') {
                    // everything is saved, so redirect to the edit action
                    $this->redirect(
                        BackendModel::createUrlForAction(
                            'Edit'
                        ) . '&id=' . $page['id'] . '&report=saved-as-draft&var=' . rawurlencode(
                            $page['title']
                        ) . '&highlight=row-' . $page['revision_id'] . '&draft=' . $page['revision_id']
                    );
                }
            }
        }
    }

    private function getImage(bool $allowImage): ?string
    {
        if (!$allowImage || !$this->form->getField('image')->isFilled()) {
            return null;
        }

        $imagePath = FRONTEND_FILES_PATH . '/Pages/images';
        $imageFilename = $this->meta->getUrl() . '_' . time() . '.' . $this->form->getField('image')->getExtension();
        $this->form->getField('image')->generateThumbnails($imagePath, $imageFilename);

        return $imageFilename;
    }

    /**
     * Check if the user has the right to see/edit tags
     *
     * @return bool
     */
    private function showTags(): bool
    {
        return Authentication::isAllowedAction('Edit', 'Tags') && Authentication::isAllowedAction('GetAllTags', 'Tags');
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
