<?php

namespace Backend\Modules\Blog\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\Filesystem\Filesystem;
use Backend\Core\Engine\Base\ActionAdd as BackendBaseActionAdd;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Meta as BackendMeta;
use Backend\Modules\Blog\Engine\Model as BackendBlogModel;
use Backend\Modules\Search\Engine\Model as BackendSearchModel;
use Backend\Modules\Tags\Engine\Model as BackendTagsModel;
use Backend\Modules\Users\Engine\Model as BackendUsersModel;

/**
 * This is the add-action, it will display a form to create a new item
 */
class Add extends BackendBaseActionAdd
{
    /**
     * Is the image field allowed?
     *
     * @var bool
     */
    protected $imageIsAllowed = true;

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
        $this->imageIsAllowed = $this->get('fork.settings')->get($this->url->getModule(), 'show_image_form', true);

        $this->form = new BackendForm('add');

        // set hidden values
        $rbtHiddenValues = [
            ['label' => BL::lbl('Hidden', $this->url->getModule()), 'value' => 1],
            ['label' => BL::lbl('Published'), 'value' => 0],
        ];

        // get categories
        $categories = BackendBlogModel::getCategories();
        $categories['new_category'] = \SpoonFilter::ucfirst(BL::getLabel('AddCategory'));

        // create elements
        $this->form->addText('title', null, null, 'form-control title', 'form-control danger title');
        $this->form->addEditor('text');
        $this->form->addEditor('introduction');
        $this->form->addRadiobutton('hidden', $rbtHiddenValues, 0);
        $this->form->addCheckbox('allow_comments', $this->get('fork.settings')->get($this->getModule(), 'allow_comments', false));
        $this->form->addDropdown('category_id', $categories, $this->getRequest()->query->getInt('category'));
        if (count($categories) !== 2) {
            $this->form->getField('category_id')->setDefaultElement('');
        }
        $this->form->addDropdown('user_id', BackendUsersModel::getUsers(), BackendAuthentication::getUser()->getUserId());
        $this->form->addText('tags', null, null, 'form-control js-tags-input', 'form-control danger js-tags-input');
        $this->form->addDate('publish_on_date');
        $this->form->addTime('publish_on_time');
        if ($this->imageIsAllowed) {
            $this->form->addImage('image');
        }

        // meta
        $this->meta = new BackendMeta($this->form, null, 'title', true);
    }

    protected function parse(): void
    {
        parent::parse();
        $this->template->assign('imageIsAllowed', $this->imageIsAllowed);

        // get url
        $url = BackendModel::getUrlForBlock($this->url->getModule(), 'detail');
        $url404 = BackendModel::getUrl(404);

        // parse additional variables
        if ($url404 != $url) {
            $this->template->assign('detailURL', SITE_URL . $url);
        }
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

            // cleanup the submitted fields, ignore fields that were added by hackers
            $this->form->cleanupFields();

            // validate fields
            $this->form->getField('title')->isFilled(BL::err('TitleIsRequired'));
            $this->form->getField('text')->isFilled(BL::err('FieldIsRequired'));
            $this->form->getField('publish_on_date')->isValid(BL::err('DateIsInvalid'));
            $this->form->getField('publish_on_time')->isValid(BL::err('TimeIsInvalid'));
            $this->form->getField('category_id')->isFilled(BL::err('FieldIsRequired'));
            if ($this->form->getField('category_id')->getValue() == 'new_category') {
                $this->form->getField('category_id')->addError(BL::err('FieldIsRequired'));
            }

            // validate meta
            $this->meta->validate();

            if ($this->form->isCorrect()) {
                // build item
                $item = [
                    'id' => (int) BackendBlogModel::getMaximumId() + 1,
                    'meta_id' => $this->meta->save(),
                    'category_id' => (int) $this->form->getField('category_id')->getValue(),
                    'user_id' => $this->form->getField('user_id')->getValue(),
                    'language' => BL::getWorkingLanguage(),
                    'title' => $this->form->getField('title')->getValue(),
                    'introduction' => $this->form->getField('introduction')->getValue(),
                    'text' => $this->form->getField('text')->getValue(),
                    'publish_on' => BackendModel::getUTCDate(
                        null,
                        BackendModel::getUTCTimestamp(
                            $this->form->getField('publish_on_date'),
                            $this->form->getField('publish_on_time')
                        )
                    ),
                    'created_on' => BackendModel::getUTCDate(),
                    'hidden' => $this->form->getField('hidden')->getValue(),
                    'allow_comments' => $this->form->getField('allow_comments')->getChecked(),
                    'num_comments' => 0,
                    'status' => $status,
                ];
                $item['edited_on'] = $item['created_on'];

                // insert the item
                $item['revision_id'] = BackendBlogModel::insert($item);

                if ($this->imageIsAllowed) {
                    // the image path
                    $imagePath = FRONTEND_FILES_PATH . '/Blog/images';

                    // create folders if needed
                    $filesystem = new Filesystem();
                    $filesystem->mkdir([$imagePath . '/source', $imagePath . '/128x128']);

                    // image provided?
                    if ($this->form->getField('image')->isFilled()) {
                        // build the image name
                        $item['image'] = $this->meta->getUrl()
                            . '-' . BL::getWorkingLanguage()
                            . '-' . $item['revision_id']
                            . '.' . $this->form->getField('image')->getExtension();

                        // upload the image & generate thumbnails
                        $this->form->getField('image')->generateThumbnails($imagePath, $item['image']);

                        // add the image to the database without changing the revision id
                        BackendBlogModel::updateRevision($item['revision_id'], ['image' => $item['image']]);
                    }
                }

                // save the tags
                BackendTagsModel::saveTags($item['id'], $this->form->getField('tags')->getValue(), $this->url->getModule());

                // active
                if ($item['status'] == 'active') {
                    // add search index
                    BackendSearchModel::saveIndex($this->getModule(), $item['id'], ['title' => $item['title'], 'text' => $item['text']]);

                    // everything is saved, so redirect to the overview
                    $this->redirect(BackendModel::createUrlForAction('Index') . '&report=added&var=' . rawurlencode($item['title']) . '&highlight=row-' . $item['revision_id']);
                } elseif ($item['status'] == 'draft') {
                    // draft: everything is saved, so redirect to the edit action
                    $this->redirect(BackendModel::createUrlForAction('Edit') . '&report=saved-as-draft&var=' . rawurlencode($item['title']) . '&id=' . $item['id'] . '&draft=' . $item['revision_id'] . '&highlight=row-' . $item['revision_id']);
                }
            }
        }
    }
}
