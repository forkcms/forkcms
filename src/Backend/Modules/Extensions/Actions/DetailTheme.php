<?php

namespace Backend\Modules\Extensions\Actions;

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\DataGridArray as BackendDataGridArray;
use Backend\Modules\Extensions\Engine\Model as BackendExtensionsModel;

/**
 * This is the detail-action it will display the details of a theme.
 */
class DetailTheme extends BackendBaseActionIndex
{
    /**
     * Theme we request the details of.
     *
     * @var string
     */
    private $currentTheme;

    /**
     * Datagrids.
     *
     * @var BackendDataGridArray
     */
    private $dataGridTemplates;

    /**
     * Information fetched from the info.xml.
     *
     * @var array
     */
    private $information = [];

    /**
     * List of warnings.
     *
     * @var array
     */
    private $warnings = [];

    public function execute(): void
    {
        // get parameters
        $this->currentTheme = $this->getRequest()->query->get('theme', '');

        // does the item exist
        if ($this->currentTheme !== '' && BackendExtensionsModel::existsTheme($this->currentTheme)) {
            parent::execute();
            $this->loadData();
            $this->loadDataGridTemplates();
            $this->parse();
            $this->display();
        } else {
            // no item found, redirect to index, because somebody is fucking with our url
            $this->redirect(BackendModel::createUrlForAction('Themes') . '&error=non-existing');
        }
    }

    /**
     * Load the data.
     * This will also set some warnings if needed.
     */
    private function loadData(): void
    {
        // inform that the theme is not installed yet
        if (!BackendExtensionsModel::isThemeInstalled($this->currentTheme)) {
            $this->warnings[] = ['message' => BL::getMessage('InformationThemeIsNotInstalled')];
        }

        // path to information file
        $pathInfoXml = FRONTEND_PATH . '/Themes/' . $this->currentTheme . '/info.xml';

        // information needs to exists
        if (is_file($pathInfoXml)) {
            try {
                // load info.xml
                $infoXml = @new \SimpleXMLElement($pathInfoXml, LIBXML_NOCDATA, true);

                // convert xml to useful array
                $this->information = BackendExtensionsModel::processThemeXml($infoXml);

                // empty data (nothing useful)
                if (empty($this->information)) {
                    $this->warnings[] = [
                        'message' => BL::getMessage('InformationFileIsEmpty'),
                    ];
                }
            } catch (\Exception $e) {
                // warning that the information file is corrupt
                $this->warnings[] = ['message' => BL::getMessage('InformationFileCouldNotBeLoaded')];
            }
        } else {
            // warning that the information file is missing
            $this->warnings[] = ['message' => BL::getMessage('InformationFileIsMissing')];
        }
    }

    private function loadDataGridTemplates(): void
    {
        // no hooks so don't bother
        if (!isset($this->information['templates'])) {
            return;
        }

        // build data for display in datagrid
        $templates = [];
        foreach ($this->information['templates'] as $template) {
            // set template name & path
            $record = [];
            $record['name'] = $template['label'];
            $record['path'] = $template['path'];

            // set positions
            $record['positions'] = [];
            foreach ($template['positions'] as $position) {
                $record['positions'][] = $position['name'];
            }
            $record['positions'] = implode(', ', $record['positions']);

            // add template to list
            $templates[] = $record;
        }

        // create data grid
        $this->dataGridTemplates = new BackendDataGridArray($templates);

        // add label for path
        $this->dataGridTemplates->setHeaderLabels(['path' => BL::msg('PathToTemplate')]);

        // no paging
        $this->dataGridTemplates->setPaging(false);
    }

    protected function parse(): void
    {
        parent::parse();

        // assign theme data
        $this->template->assign('name', $this->currentTheme);
        $this->template->assign('warnings', $this->warnings);
        $this->template->assign('information', $this->information);
        $this->template->assign(
            'showInstallButton',
            !BackendExtensionsModel::isThemeInstalled($this->currentTheme) && BackendAuthentication::isAllowedAction(
                'InstallTheme'
            )
        );

        // data grids
        $this->template->assign(
            'dataGridTemplates',
            (isset($this->dataGridTemplates) && $this->dataGridTemplates->getNumResults() > 0)
                ? $this->dataGridTemplates->getContent()
                : false
        );
    }
}
