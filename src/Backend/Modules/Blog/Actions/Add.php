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

    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();
        $this->loadForm();
        $this->validateForm();
        $this->parse();
        $this->display();
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        $this->imageIsAllowed = $this->get('fork.settings')->get($this->URL->getModule(), 'show_image_form', true);

        $this->frm = new BackendForm('add');

        // set hidden values
        $rbtHiddenValues[] = array('label' => BL::lbl('Hidden', $this->URL->getModule()), 'value' => 'Y');
        $rbtHiddenValues[] = array('label' => BL::lbl('Published'), 'value' => 'N');

        // get categories
        $categories = BackendBlogModel::getCategories();
        $categories['new_category'] = \SpoonFilter::ucfirst(BL::getLabel('AddCategory'));

        // create elements
        $this->frm->addText('title', null, null, 'form-control title', 'form-control danger title');
        $this->frm->addEditor('text');
        $this->frm->addEditor('introduction');
        $this->frm->addRadiobutton('hidden', $rbtHiddenValues, 'N');
        $this->frm->addCheckbox('allow_comments', $this->get('fork.settings')->get($this->getModule(), 'allow_comments', false));
        $this->frm->addDropdown('category_id', $categories, \SpoonFilter::getGetValue('category', null, null, 'int'));
        if (count($categories) != 2) {
            $this->frm->getField('category_id')->setDefaultElement('');
        }
        $this->frm->addDropdown('user_id', BackendUsersModel::getUsers(), BackendAuthentication::getUser()->getUserId());
        $this->frm->addText('tags', null, null, 'form-control js-tags-input', 'form-control danger js-tags-input');
        $this->frm->addDate('publish_on_date');
        $this->frm->addTime('publish_on_time');
        if ($this->imageIsAllowed) {
            $this->frm->addImage('image');
        }

        // meta
        $this->meta = new BackendMeta($this->frm, null, 'title', true);
    }

    /**
     * Parse the form
     */
    protected function parse()
    {
        parent::parse();
        $this->tpl->assign('imageIsAllowed', $this->imageIsAllowed);

        // get url
        $url = BackendModel::getURLForBlock($this->URL->getModule(), 'detail');
        $url404 = BackendModel::getURL(404);

        // parse additional variables
        if ($url404 != $url) {
            $this->tpl->assign('detailURL', SITE_URL . $url);
        }
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

            // cleanup the submitted fields, ignore fields that were added by hackers
            $this->frm->cleanupFields();

            // validate fields
            $this->frm->getField('title')->isFilled(BL::err('TitleIsRequired'));
            $this->frm->getField('text')->isFilled(BL::err('FieldIsRequired'));
            $this->frm->getField('publish_on_date')->isValid(BL::err('DateIsInvalid'));
            $this->frm->getField('publish_on_time')->isValid(BL::err('TimeIsInvalid'));
            $this->frm->getField('category_id')->isFilled(BL::err('FieldIsRequired'));
            if ($this->frm->getField('category_id')->getValue() == 'new_category') {
                $this->frm->getField('category_id')->addError(BL::err('FieldIsRequired'));
            }

            // validate meta
            $this->meta->validate();

            if ($this->frm->isCorrect()) {
                // build item
                $item['id'] = (int) BackendBlogModel::getMaximumId() + 1;
                $item['meta_id'] = $this->meta->save();
                $item['category_id'] = (int) $this->frm->getField('category_id')->getValue();
                $item['user_id'] = $this->frm->getField('user_id')->getValue();
                $item['language'] = BL::getWorkingLanguage();
                $item['title'] = $this->frm->getField('title')->getValue();
                $item['introduction'] = $this->frm->getField('introduction')->getValue();
                $item['text'] = $this->frm->getField('text')->getValue();
                $item['publish_on'] = BackendModel::getUTCDate(null, BackendModel::getUTCTimestamp($this->frm->getField('publish_on_date'), $this->frm->getField('publish_on_time')));
                $item['created_on'] = BackendModel::getUTCDate();
                $item['edited_on'] = $item['created_on'];
                $item['hidden'] = $this->frm->getField('hidden')->getValue();
                $item['allow_comments'] = $this->frm->getField('allow_comments')->getChecked() ? 'Y' : 'N';
                $item['num_comments'] = 0;
                $item['status'] = $status;

                // insert the item
                $item['revision_id'] = BackendBlogModel::insert($item);

                if ($this->imageIsAllowed) {
                    // the image path
                    $imagePath = FRONTEND_FILES_PATH . '/blog/images';

                    // create folders if needed
                    $filesystem = new Filesystem();
                    $filesystem->mkdir(array($imagePath . '/source', $imagePath . '/128x128'));

                    // image provided?
                    if ($this->frm->getField('image')->isFilled()) {
                        // build the image name
                        $item['image'] = $this->meta->getURL()
                            . '-' . BL::getWorkingLanguage()
                            . '-' . $item['revision_id']
                            . '.' . $this->frm->getField('image')->getExtension();

                        // upload the image & generate thumbnails
                        $this->frm->getField('image')->generateThumbnails($imagePath, $item['image']);

                        // add the image to the database without changing the revision id
                        BackendBlogModel::updateRevision($item['revision_id'], array('image' => $item['image']));
                    }
                }

                // save the tags
                BackendTagsModel::saveTags($item['id'], $this->frm->getField('tags')->getValue(), $this->URL->getModule());

                // active
                if ($item['status'] == 'active') {
                    // add search index
                    BackendSearchModel::saveIndex($this->getModule(), $item['id'], array('title' => $item['title'], 'text' => $item['text']));

                    // everything is saved, so redirect to the overview
                    $this->redirect(BackendModel::createURLForAction('Index') . '&report=added&var=' . rawurlencode($item['title']) . '&highlight=row-' . $item['revision_id']);
                } elseif ($item['status'] == 'draft') {
                    // draft: everything is saved, so redirect to the edit action
                    $this->redirect(BackendModel::createURLForAction('Edit') . '&report=saved-as-draft&var=' . rawurlencode($item['title']) . '&id=' . $item['id'] . '&draft=' . $item['revision_id'] . '&highlight=row-' . $item['revision_id']);
                }
            }
        }
    }
}
