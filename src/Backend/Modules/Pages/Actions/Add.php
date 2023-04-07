<?php

namespace Backend\Modules\Pages\Actions;

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
use Backend\Modules\Profiles\Engine\Model as BackendProfilesModel;
use Common\Core\Model;
use ForkCMS\Utility\Thumbnails;
use SpoonFormHidden;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;

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
     * Original image from the page we are cloning
     *
     * @var string|null
     */
    private $originalImage;

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
     * The hreflang fields
     *
     * @var array
     */
    private $hreflangFields = [];

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

        $originalPage = $this->getOriginalPage();

        // assign in template
        $this->template->assign('defaultTemplateId', $defaultTemplateId);

        // assign if profiles module is installed
        $this->template->assign('showAuthenticationTab', BackendModel::isModuleInstalled('Profiles'));

        $this->form->addText(
            'title',
            isset($originalPage['title']) ? sprintf(BL::msg('CopiedTitle'), $originalPage['title']) : null,
            null,
            'form-control title',
            'form-control danger title'
        );
        $this->form->addEditor('html');
        $this->form->addHidden('template_id', $originalPage['template_id'] ?? $defaultTemplateId);
        $this->form->addRadiobutton(
            'hidden',
            [
                ['label' => BL::lbl('Hidden'), 'value' => 1],
                ['label' => BL::lbl('Published'), 'value' => 0],
            ],
            $originalPage['hidden'] ?? 0
        );

        // image related fields
        $this->form->addImage('image')->setAttribute('data-fork-cms-role', 'image-field');
        if ($originalPage !== null) {
            $this->form->addCheckbox('remove_image');
            $this->originalImage = $originalPage['data']['image'] ?? null;
            $this->template->assign('originalImage', $this->originalImage);
        }

        // just execute if the site is multi-language
        if ($this->getContainer()->getParameter('site.multilanguage')) {
            // loop active languages
            foreach (BL::getActiveLanguages() as $language) {
                if ($language !== BL::getWorkingLanguage()) {
                    $pages = BackendPagesModel::getPagesForDropdown($language);
                    // add field for each language
                    $field = $this->form->addDropdown('hreflang_' . $language, $pages)->setDefaultElement('');
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
                $originalPage !== null && isset($originalPage['data']['auth_required']) && $originalPage['data']['auth_required']
            );

            // add checkbox for index page to search
            $this->form->addCheckbox(
                'remove_from_search_index',
                $originalPage !== null && isset($originalPage['data']['remove_from_search_index']) && $originalPage['data']['remove_from_search_index']
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
                if ($originalPage !== null && isset($originalPage['data']['auth_groups'])
                    && is_array($originalPage['data']['auth_groups'])) {
                    foreach ($originalPage['data']['auth_groups'] as $group) {
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
            $checked = [];
            $values = [];

            foreach ($permissions as $permission => $attributes) {
                $allowPermission = 'allow_' . $permission;
                $values[] = [
                    'label' => BL::msg(\SpoonFilter::toCamelCase($allowPermission)),
                    'value' => $permission,
                    'attributes' => $attributes,
                ];

                if ($originalPage === null || (isset($originalPage[$allowPermission]) && $originalPage[$allowPermission])) {
                    $checked[] = $permission;
                }
            }

            $this->form->addMultiCheckbox('allow', $values, $checked);

            // css link class
            $this->form->addText('link_class');
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
                if ($block['extra_id'] === null || $block['extra_type'] === 'usertemplate') {
                    if ($this->getRequest()->request->get('block_extra_type_' . $i) === 'usertemplate') {
                        $block['extra_id'] = $this->getRequest()->request->get('block_extra_id_' . $i);
                        $_POST['block_extra_data_' . $i] = htmlspecialchars($_POST['block_extra_data_' . $i]);
                    } else {
                        // reset vars
                        $block['extra_id'] = null;
                    }
                    $block['html'] = $html;
                } elseif (isset($this->extras[$block['extra_id']]['type']) && $this->extras[$block['extra_id']]['type'] === 'block') {
                    // set error
                    if ($hasBlock) {
                        $this->form->addError(BL::err('CantAdd2Blocks'));
                    }

                    // reset var
                    $hasBlock = true;
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
        if ($originalPage !== null && isset($originalPage['data']['internal_redirect']['page_id'])) {
            $redirectValue = 'internal';
        }
        if ($originalPage !== null && isset($originalPage['data']['external_redirect']['url'])) {
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
            ($redirectValue === 'internal') ? $originalPage['data']['internal_redirect']['page_id'] : null
        );
        $this->form->addText(
            'external_redirect',
            ($redirectValue === 'external') ? urldecode($originalPage['data']['external_redirect']['url']) : null,
            null,
            null,
            null,
            true
        );

        // page info
        $this->form->addCheckbox('navigation_title_overwrite', $originalPage['navigation_title_overwrite'] ?? null);
        $this->form->addText('navigation_title', $originalPage['navigation_title'] ?? null);

        if ($this->showTags()) {
            // tags
            $this->form->addText(
                'tags',
                $originalPage === null ? null : BackendTagsModel::getTags($this->url->getModule(), $originalPage['id']),
                null,
                'form-control js-tags-input',
                'form-control danger js-tags-input'
            )->setAttribute('aria-describedby', 'tags-info');
        }

        // a specific action
        $isAction = $originalPage !== null && isset($originalPage['data']['is_action']) && $originalPage['data']['is_action'];
        $this->form->addCheckbox('is_action', $isAction);

        // extra
        $blockTypes = BackendPagesModel::getTypes();
        $this->form->addDropdown('extra_type', $blockTypes, key($blockTypes));

        // meta
        $this->meta = new BackendMeta($this->form, null, 'title', true);

        // set callback for generating an unique URL
        $this->meta->setUrlCallback(
            BackendPagesModel::class,
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
        $this->template->assign('extrasData', json_encode(BackendModel::recursiveHtmlspecialchars(BackendExtensionsModel::getExtrasData())));
        $this->template->assign('extrasById', json_encode(BackendExtensionsModel::getExtras()));
        $this->template->assign(
            'prefixURL',
            rtrim(
                BackendPagesModel::getFullUrl($this->getRequest()->query->getInt('parent', BackendModel::HOME_PAGE_ID)),
                '/'
            )
        );
        $this->template->assign('formErrors', (string) $this->form->getErrors());
        $this->template->assign('showTags', $this->showTags());
        $this->template->assign('hreflangFields', $this->hreflangFields);

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
                [0, $this->getRequest()->query->getInt('parent'), $this->form->getField('is_action')->getChecked()]
            );

            // cleanup the submitted fields, ignore fields that were added by hackers
            $this->form->cleanupFields();

            // validate fields
            $this->form->getField('title')->isFilled(BL::err('TitleIsRequired'));
            if ($this->form->getField('navigation_title_overwrite')->isChecked()) {
                $this->form->getField('navigation_title')->isFilled(BL::err('FieldIsRequired'));
            }
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
                if ($redirectValue === 'internal') {
                    $data['internal_redirect'] = [
                        'page_id' => $this->form->getField('internal_redirect')->getValue(),
                        'code' => Response::HTTP_TEMPORARY_REDIRECT,
                    ];
                }
                if ($redirectValue === 'external') {
                    $data['external_redirect'] = [
                        'url' => BackendPagesModel::getEncodedRedirectUrl(
                            $this->form->getField('external_redirect')->getValue()
                        ),
                        'code' => Response::HTTP_TEMPORARY_REDIRECT,
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
                        if ($language !== BL::getWorkingLanguage() && $this->form->getfield('hreflang_' . $language)->isFilled()) {
                            $data['hreflang_' . $language] = $this->form->getfield('hreflang_' . $language)->getValue();
                        }
                    }
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
                $page['navigation_title'] = ($this->form->getField('navigation_title')->getValue() != '')
                    ? $this->form->getField('navigation_title')->getValue()
                    : $this->form->getField('title')->getValue();
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

                    // link class
                    $data['link_class'] = $this->form->getField('link_class')->getValue();
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
                if ($page['status'] === 'active') {
                    $this->saveSearchIndex($data['remove_from_search_index'] || $redirectValue !== 'none', $page);

                    // everything is saved, so redirect to the overview
                    $this->redirect(
                        BackendModel::createUrlForAction(
                            'Edit'
                        ) . '&id=' . $page['id'] . '&report=added&var=' . rawurlencode(
                            $page['title']
                        ) . '&highlight=row-' . $page['id']
                    );
                } elseif ($page['status'] === 'draft') {
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
        if (!$allowImage
            || (!$this->form->getField('image')->isFilled() && $this->originalImage === null)
            || ($this->originalImage !== null && $this->form->getField('remove_image')->isChecked())) {
            return null;
        }

        $imagePath = FRONTEND_FILES_PATH . '/Pages/images';

        if ($this->originalImage !== null && !$this->form->getField('image')->isFilled()) {
            $originalImagePath = $imagePath . '/source/' . $this->originalImage;
            $imageFilename = $this->getImageFilenameForExtension(pathinfo($originalImagePath, PATHINFO_EXTENSION));
            $newImagePath = $imagePath . '/source/' . $imageFilename;

            // make sure we have a separate image for the copy in case the original image gets removed
            (new Filesystem())->copy($originalImagePath, $newImagePath);
            $this->get(Thumbnails::class)->generate($imagePath, $newImagePath);

            return $imageFilename;
        }

        $imageFilename = $this->getImageFilenameForExtension($this->form->getField('image')->getExtension());
        $this->form->getField('image')->generateThumbnails($imagePath, $imageFilename);

        return $imageFilename;
    }

    private function getImageFilenameForExtension(string $extension): string
    {
        return $this->meta->getUrl() . '_' . time() . '.' . $extension;
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

    private function getOriginalPage(): ?array
    {
        $id = $this->getRequest()->query->getInt('copy');

        // check if the page exists
        if ($id === 0 || !BackendPagesModel::exists($id)) {
            return null;
        }

        $this->template->assign('showCopyWarning', true);

        $originalPage = BackendPagesModel::get($id);

        // Handle usertemplate images on copying a page, as otherwise the same path will be used.
        // On changing/deleting a copied usertemplate image, the old image is deleted, breaking the usertemplate
        // with the same image on the original page
        $this->blocksContent = array_map(
            function (array $block) {
                // Only for usertemplates
                if ($block['extra_type'] !== 'usertemplate') {
                    return $block;
                }

                // Only usertemplates with image
                if (strpos($block['html'], 'data-ft-type="image"') === false) {
                    return $block;
                }

                // Find images in usertemplate
                $blockElements = new Crawler($block['html']);
                $images = $blockElements->filter('[data-ft-type="image"]');
                $filesystem = new Filesystem();
                $path = FRONTEND_FILES_PATH . '/Pages/UserTemplate';
                $url = FRONTEND_FILES_URL . '/Pages/UserTemplate';
                foreach ($images as $image) {
                    $imagePath = $image->getAttribute('src');

                    // skip empty images
                    if ($imagePath === '') {
                        continue;
                    }

                    $basename = pathinfo($imagePath, PATHINFO_FILENAME);
                    $extension = pathinfo($imagePath, PATHINFO_EXTENSION);
                    $originalFilename = $basename . '.' . $extension;
                    $filename = $originalFilename;

                    // Generate a non-existing filename
                    while ($filesystem->exists($path . '/' . $filename)) {
                        $basename = Model::addNumber($basename);
                        $filename = $basename . '.' . $extension;
                    }

                    $block['html'] = str_replace($imagePath, $url . '/' . $filename, $block['html']);
                    $filesystem->copy($path . '/' . $originalFilename, $path . '/' . $filename);
                }

                return $block;
            },
            BackendPagesModel::getBlocks($id, $originalPage['revision_id'])
        );

        return $originalPage;
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
}
