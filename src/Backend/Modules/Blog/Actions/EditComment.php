<?php

namespace Backend\Modules\Blog\Actions;

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Language\Language as BL;
use Backend\Modules\Blog\Engine\Model as BackendBlogModel;

class EditComment extends BackendBaseActionEdit
{
    public function execute(): void
    {
        parent::execute();
        $this->getData();
        $this->handleForm();
        $this->display();
    }

    private function getData(): void
    {
        $this->id = $this->getRequest()->query->getInt('id');

        // does the item exist
        if ($this->id === 0 || !BackendBlogModel::existsComment($this->id)) {
            $this->redirect(BackendModel::createUrlForAction('Index') . '&error=non-existing');

            return;
        }

        $this->record = BackendBlogModel::getComment($this->id);

        // Redirect to the index page if no comment was found
        if (empty($this->record)) {
            $this->redirect(BackendModel::createUrlForAction('Index') . '&error=non-existing');
        }
    }

    private function handleForm()
    {
        $this->form = $this->buildForm();

        if (!$this->form->isSubmitted() || !$this->validateForm()) {
            $this->parse();

            return;
        }

        $comment = [
            'id' => $this->id,
            'status' => $this->record['status'],
            'author' => $this->form->getField('author')->getValue(),
            'email' => $this->form->getField('email')->getValue(),
            'website' => $this->form->getField('website')->isFilled()
                ? $this->form->getField('website')->getValue() : null,
            'text' => $this->form->getField('text')->getValue(),
            'data' => null,
        ];

        if ($this->form->getField('enableReplyAndSendNotificationToAuthor')->isFilled()) {
            $comment['data']['reply'] = $this->form->getField('reply')->getValue();
        }

        BackendBlogModel::updateComment($comment);

        $this->redirect(
            BackendModel::createUrlForAction('Comments') . '&report=edited-comment&id=' .
            $comment['id'] . '&highlight=row-' . $comment['id'] . '#tab' .
            \SpoonFilter::toCamelCase($comment['status'])
        );
    }

    private function buildForm(): BackendForm
    {
        $form = new BackendForm('editComment');

        $form->addText('author', $this->record['author']);
        $form->addText('email', $this->record['email']);
        $form->addText('website', $this->record['website'], null);
        $form->addTextarea('text', $this->record['text']);

        $form->addCheckbox(
            'enableReplyAndSendNotificationToAuthor',
            $this->alreadyHasReply()
        )->setAttribute('data-role', 'enable-reply');
        $form->addTextarea('reply', $this->record['data']['reply'] ?? null);

        return $form;
    }

    private function alreadyHasReply(): bool
    {
        return isset($this->record['data']['reply']);
    }

    protected function parse(): void
    {
        parent::parse();

        $this->template->assign(
            'itemURL',
            BackendModel::getUrlForBlock($this->getModule(), 'detail') . '/' .
            $this->record['post_url'] . '#comment-' . $this->record['post_id']
        );
        $this->template->assign('itemTitle', $this->record['post_title']);
    }

    private function validateForm(): bool
    {
        $this->form->cleanupFields();

        $this->form->getField('author')->isFilled(BL::err('AuthorIsRequired'));
        $this->form->getField('email')->isEmail(BL::err('EmailIsInvalid'));
        $this->form->getField('text')->isFilled(BL::err('FieldIsRequired'));
        if ($this->form->getField('website')->isFilled()) {
            $this->form->getField('website')->isURL(BL::err('InvalidURL'));
        }

        if ($this->form->getField('enableReplyAndSendNotificationToAuthor')->isFilled()) {
            $this->form->getField('reply')->isFilled(BL::err('FieldIsRequired'));
        }

        return $this->form->isCorrect();
    }
}
