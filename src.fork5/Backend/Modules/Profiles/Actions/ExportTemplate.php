<?php

namespace Backend\Modules\Profiles\Actions;

use Backend\Core\Engine\Authentication;
use Backend\Core\Engine\Base\ActionAdd as BackendBaseActionAdd;
use Common\Exception\RedirectException;
use ForkCMS\Utility\Csv\Writer;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 * This is the add-action, it will display a form to add a new profile.
 */
class ExportTemplate extends BackendBaseActionAdd
{
    public function execute(): void
    {
        $this->checkToken();

        $spreadSheet = new Spreadsheet();
        $sheet = $spreadSheet->getActiveSheet();
        $sheet->fromArray(
            [
                'email',
                'display_name',
                'password',
            ],
            null,
            'A1'
        );

        throw new RedirectException(
            'Return the csv data',
            $this->get(Writer::class)
                ->getResponseForUser(
                    $spreadSheet,
                    'import_template.csv',
                    Authentication::getUser()
                )
        );
    }
}
