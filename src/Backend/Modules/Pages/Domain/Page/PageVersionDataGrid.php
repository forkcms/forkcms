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
        $whereStatement = 'p.id = :id AND p.status = :status AND p.locale = :locale';
        $parameters = [
            'id' => $page->getId(),
            'status' => $status,
            'locale' => $page->getLocale(),
        ];

        if ($status->isDraft()) {
            $whereStatement .= ' AND p.user_id = :authenticatedUserId';
            $parameters['authenticatedUserId'] = Authentication::getUser()->getUserId();
        }

        parent::__construct(
            'SELECT p.id, p.revision_id, p.title, UNIX_TIMESTAMP(p.edited_on) AS edited_on, p.user_id
             FROM PagesPage AS p
             WHERE ' . $whereStatement . '
             ORDER BY p.edited_on DESC',
            $parameters
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
        if (Authentication::isAllowedAction('PageEdit')) {
            $key = $status->isDraft() ? 'draft' : 'revision';
            $label = $status->isDraft() ? Language::lbl('UseThisDraft') : Language::lbl('UseThisVersion');
            $this->setColumnURL(
                'title',
                Model::createUrlForAction('PageEdit') . '&amp;id=[id]&amp;' . $key . '=[revision_id]'
            );

            $this->addColumn(
                'use_' . $key,
                null,
                $label,
                Model::createUrlForAction('PageEdit') . '&amp;id=[id]&amp;' . $key . '=[revision_id]',
                $label
            );
        }
    }

    public static function getHtml(Page $page, Status $status): string
    {
        return (new self($page, $status))->getContent();
    }
}
