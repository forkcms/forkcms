<?php

namespace Backend\Modules\ContentBlocks\Domain\ContentBlock;

use Backend\Core\Engine\DataGridDatabase;
use Backend\Core\Engine\TemplateModifiers;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use App\Component\Model\BackendModel;
use App\Component\Locale\BackendLanguage;
use App\Component\Locale\BackendLocale;

/**
 * @TODO replace with a doctrine implementation of the data grid
 */
class ContentBlockDataGrid extends DataGridDatabase
{
    public function __construct(BackendLocale $locale)
    {
        parent::__construct(
            'SELECT i.id, i.title, i.hidden
             FROM content_blocks AS i
             WHERE i.status = :active AND i.language = :language',
            ['active' => Status::active(), 'language' => $locale]
        );

        $this->setSortingColumns(['title']);

        // show the hidden status
        $this->addColumn('isHidden', ucfirst(BackendLanguage::lbl('VisibleOnSite')), '[hidden]');
        $this->setColumnFunction([TemplateModifiers::class, 'showBool'], ['[hidden]', true], 'isHidden');

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('Edit')) {
            $editUrl = BackendModel::createUrlForAction('Edit', null, null, ['id' => '[id]'], false);
            $this->setColumnURL('title', $editUrl);
            $this->addColumn('edit', null, BackendLanguage::lbl('Edit'), $editUrl, BackendLanguage::lbl('Edit'));
        }
    }

    public static function getHtml(BackendLocale $locale): string
    {
        return (new self($locale))->getContent();
    }
}
