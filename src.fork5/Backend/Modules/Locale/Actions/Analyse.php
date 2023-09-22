<?php

namespace Backend\Modules\Locale\Actions;

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\DataGridArray as BackendDataGridArray;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Locale\Engine\AnalyseModel as BackendLocaleModel;

/**
 * This is the analyse-action, it will display an overview of used locale.
 */
class Analyse extends BackendBaseActionIndex
{
    /**
     * @var BackendDataGridArray
     */
    private $dgBackend;

    /**
     * @var BackendDataGridArray
     */
    private $dgFrontend;

    public function execute(): void
    {
        parent::execute();
        $this->loadDataGrids();
        $this->parse();
        $this->display();
    }

    /**
     * Format a serialized path-array into something that is usable in a datagrid
     *
     * @param string $files The serialized array with the paths.
     *
     * @return string
     */
    public static function formatFilesList(string $files): string
    {
        $files = (array) unserialize($files, ['allowed_classes' => false]);

        // no files
        if (empty($files)) {
            return '';
        }

        // start
        $return = '<ul>' . "\n";

        // loop files
        foreach ($files as $file) {
            $return .= '<li><code title="' . str_replace(PATH_WWW, '', $file) . '">' . wordwrap(str_replace(PATH_WWW, '', $file), 80, '<br />', true) . '</code></li>' . "\n";
        }

        // end
        $return .= '</ul>';

        // cleanup
        return $return;
    }

    private function loadDataGrids(): void
    {
        /*
         * Frontend datagrid
         */
        $nonExistingFrontendLocale = BackendLocaleModel::getNonExistingFrontendLocale(BL::getWorkingLanguage());
        $this->dgFrontend = new BackendDataGridArray($nonExistingFrontendLocale);

        // overrule default URL
        $this->dgFrontend->setURL(BackendModel::createUrlForAction(null, null, null, ['offset' => '[offset]', 'order' => '[order]', 'sort' => '[sort]'], false));

        // sorting columns
        $this->dgFrontend->setSortingColumns(['language', 'application', 'module', 'type', 'name'], 'name');

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('Add')) {
            // set column URLs
            $this->dgFrontend->setColumnURL('name', BackendModel::createUrlForAction('Add') . '&amp;language=[language]&amp;application=[application]&amp;module=[module]&amp;type=[type]&amp;name=[name]');
        }

        // set column functions
        $this->dgFrontend->setColumnFunction([__CLASS__, 'formatFilesList'], '[used_in]', 'used_in', true);

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('SaveTranslation')) {
            // add columns
            $this->dgFrontend->addColumn('translation', null, null, null, BL::lbl('Add'));

            // add a class for the inline edit
            $this->dgFrontend->setColumnAttributes('translation', ['class' => 'translationValue']);

            // add attributes, so the inline editing has all the needed data
            $this->dgFrontend->setColumnAttributes('translation', ['data-id' => '{language: \'[language]\', application: \'[application]\', module: \'[module]\', name: \'[name]\', type: \'[type]\'}']);
            $this->dgFrontend->setColumnAttributes('translation', ['style' => 'width: 150px']);
        }

        // disable paging
        $this->dgFrontend->setPaging(false);

        /*
         * Backend datagrid
         */
        $getNonExistingBackendLocale = BackendLocaleModel::getNonExistingBackendLocale(BL::getWorkingLanguage());
        $this->dgBackend = new BackendDataGridArray($getNonExistingBackendLocale);

        // overrule default URL
        $this->dgBackend->setURL(BackendModel::createUrlForAction(null, null, null, ['offset' => '[offset]', 'order' => '[order]', 'sort' => '[sort]'], false));

        // sorting columns
        $this->dgBackend->setSortingColumns(['language', 'application', 'module', 'type', 'name'], 'name');

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('Add')) {
            // set column URLs
            $this->dgBackend->setColumnURL('name', BackendModel::createUrlForAction('Add') . '&amp;language=[language]&amp;application=[application]&amp;module=[module]&amp;type=[type]&amp;name=[name]');
        }

        // set column functions
        $this->dgBackend->setColumnFunction([__CLASS__, 'formatFilesList'], '[used_in]', 'used_in', true);

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('SaveTranslation')) {
            // add columns
            $this->dgBackend->addColumn('translation', null, null, null, BL::lbl('Add'));

            // add a class for the inline edit
            $this->dgBackend->setColumnAttributes('translation', ['class' => 'translationValue']);

            // add attributes, so the inline editing has all the needed data
            $this->dgBackend->setColumnAttributes('translation', ['data-id' => '{language: \'[language]\', application: \'[application]\', module: \'[module]\', name: \'[name]\', type: \'[type]\'}']);
            $this->dgBackend->setColumnAttributes('translation', ['style' => 'width: 150px']);
        }

        // disable paging
        $this->dgBackend->setPaging(false);
    }

    protected function parse(): void
    {
        parent::parse();

        // parse datagrid
        $this->template->assign('dgBackend', ($this->dgBackend->getNumResults() != 0) ? $this->dgBackend->getContent() : false);
        $this->template->assign('dgFrontend', ($this->dgFrontend->getNumResults() != 0) ? $this->dgFrontend->getContent() : false);

        // parse filter
        $this->template->assign('language', BL::getWorkingLanguage());
    }
}
