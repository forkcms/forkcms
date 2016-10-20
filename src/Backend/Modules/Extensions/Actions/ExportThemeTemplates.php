<?php

namespace Backend\Modules\Extensions\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionEdit;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Extensions\Engine\Model;
use SpoonFilter;
use Symfony\Component\HttpFoundation\Response;

/**
 * Exports templates in the selected theme, for ease when packaging themes.
 */
class ExportThemeTemplates extends ActionEdit
{
    /**
     * All available themes
     *
     * @var    array
     */
    private $availableThemes;

    /**
     * The current selected theme
     *
     * @var    string
     */
    private $selectedTheme;

    /**
     * Load the selected theme, falling back to default if none specified.
     */
    public function execute()
    {
        // get data
        $this->selectedTheme = $this->getParameter('theme', 'string');

        // build available themes
        foreach (Model::getThemes() as $theme) {
            $this->availableThemes[$theme['value']] = $theme['label'];
        }

        // determine selected theme, based upon submitted form or default theme
        $this->selectedTheme = SpoonFilter::getValue(
            $this->selectedTheme,
            array_keys($this->availableThemes),
            $this->get('fork.settings')->get('Core', 'theme', 'core')
        );
    }

    /**
     * @return Response
     */
    public function getContent()
    {
        $filename = 'templates_' . BackendModel::getUTCDate('d-m-Y') . '.xml';

        return new Response(
            Model::createTemplateXmlForExport($this->selectedTheme),
            Response::HTTP_OK,
            [
                'Content-type' => 'text/xml',
                'Content-disposition' => 'attachment; filename="' . $filename . '"',
            ]
        );
    }
}
