<?php

namespace Backend\Modules\Pages\Domain\Page;

use Backend\Core\Engine\Authentication;
use Backend\Core\Engine\DataGridDatabase;
use Backend\Core\Engine\DataGridFunctions;
use Backend\Core\Engine\Model;
use Backend\Core\Language\Language;

class PageVersionDataGrid extends DataGridDatabase
{
    public function __construct(Page $page, Status $status)
    {
        parent::__construct(
            'SELECT i.id, i.revision_id, i.title, UNIX_TIMESTAMP(i.edited_on) AS edited_on, i.user_id
             FROM PagesPage AS i
             WHERE i.id = :id AND i.status = :status AND i.locale = :locale
             ORDER BY i.edited_on DESC',
            [
                'id' => $page->getId(),
                'status' => $status,
                'locale' => $page->getLocale(),
            ]
        );

        $this->setColumnsHidden(['id', 'revision_id']);
        $this->setPaging(false);
        $this->setHeaderLabels(
            [
                'user_id' => \SpoonFilter::ucfirst(Language::lbl('By')),
                'edited_on' => \SpoonFilter::ucfirst(Language::lbl('LastEditedOn')),
            ]
        );

        $this->setColumnFunction([DataGridFunctions::class, 'getUser'], ['[user_id]'], 'user_id');
        $this->setColumnFunction([DataGridFunctions::class, 'getTimeAgo'], ['[edited_on]'], 'edited_on');

        // our JS needs to know an id, so we can highlight it
        $this->setRowAttributes(['id' => 'row-[revision_id]']);

        // check if this action is allowed
        if (Authentication::isAllowedAction('Edit')) {
            $key = $status->isDraft() ? 'draft' : 'revision';
            $label = $status->isDraft() ? Language::lbl('UseThisDraft') : Language::lbl('UseThisVersion');
            $this->setColumnURL(
                'title',
                Model::createUrlForAction('Edit') . '&amp;id=[id]&amp;' . $key . '=[revision_id]'
            );

            $this->addColumn(
                'use_' . $key,
                null,
                $label,
                Model::createUrlForAction('Edit') . '&amp;id=[id]&amp;' . $key . '=[revision_id]',
                $label
            );
        }
    }

    public static function getHtml(Page $page, Status $status): string
    {
        return (new self($page, $status))->getContent();
    }
}
