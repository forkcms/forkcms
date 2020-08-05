<?php

namespace ForkCMS\Utility\Csv;

use Backend\Core\Engine\Authentication;
use Backend\Core\Engine\User;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Writer
{
    private $charset;

    private $options = [];

    public function __construct(string $charset)
    {
        $this->charset = $charset;
        $this->setDefaultOptions();
    }

    private function setDefaultOptions()
    {
        $this->options['Enclosure'] = '"';
        $this->options['Delimiter'] = ',';
        $this->options['LineEnding'] = "\n";
        $this->options['UseBOM'] = true;
    }

    private function getWriter(Spreadsheet $spreadsheet): Csv
    {
        $writer = IOFactory::createWriter($spreadsheet, 'Csv');

        if (!empty($this->options)) {
            foreach ($this->options as $option => $value) {
                $methodName = 'set' . $option;
                if (method_exists($writer, $methodName)) {
                    call_user_func([$writer, $methodName], $value);
                }
            }
        }

        return $writer;
    }

    public function forBackendUser(User $user): self
    {
        $this->options['Delimiter'] = $user->getSetting('csv_split_character');

        $lineEnding = Authentication::getUser()->getSetting('csv_line_ending');
        if ($lineEnding === '\n') {
            $this->options['LineEnding'] = "\n";
        }
        if ($lineEnding === '\r\n') {
            $this->options['LineEnding'] = "\r\n";
        }

        return $this;
    }

    public function output(Spreadsheet $spreadsheet, string $filename): StreamedResponse
    {
        $writer = $this->getWriter($spreadsheet);

        $response = new StreamedResponse(
            function () use ($writer) {
                $writer->save('php://output');
            }
        );

        // set headers
        $response->headers->set('Content-type', 'application/csv; charset=' . $this->charset);
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $response->headers->set('Cache-Control', 'max-age=0');
        $response->headers->set('Pragma', 'no-cache');

        return $response;
    }
}
