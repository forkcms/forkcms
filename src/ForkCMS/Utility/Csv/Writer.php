<?php

namespace ForkCMS\Utility\Csv;

use Backend\Core\Engine\Authentication;
use Backend\Core\Engine\User;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipStream\Stream;

class Writer
{
    private $charset;

    public function __construct(string $charset)
    {
        $this->charset = $charset;
    }

    private function getDefaultOptions(): array
    {
        $options['Enclosure'] = '"';
        $options['Delimiter'] = ',';
        $options['LineEnding'] = "\n";
        $options['UseBOM'] = true;

        return $options;
    }

    private function getUserOptions(User $user): array
    {
        $options['Delimiter'] = $user->getSetting('csv_split_character');

        $lineEnding = $user->getSetting('csv_line_ending');
        if ($lineEnding === '\n') {
            $options['LineEnding'] = "\n";
        }
        if ($lineEnding === '\r\n') {
            $options['LineEnding'] = "\r\n";
        }

        return $options;
    }

    private function getWriter(Spreadsheet $spreadsheet, array $options = []): Csv
    {
        $writer = IOFactory::createWriter($spreadsheet, 'Csv');

        if (!empty($options)) {
            foreach ($options as $option => $value) {
                $methodName = 'set' . $option;
                if (method_exists($writer, $methodName)) {
                    call_user_func([$writer, $methodName], $value);
                }
            }
        }

        return $writer;
    }

    private function getStreamedResponse(Csv $writer, string $filename): StreamedResponse
    {
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

    public function getResponse(Spreadsheet $spreadsheet, string $filename, array $options = []): StreamedResponse
    {
        $options = array_merge($this->getDefaultOptions(), $options);

        return $this->getStreamedResponse(
            $this->getWriter($spreadsheet, $options),
            $filename
        );
    }

    public function getResponseForUser(Spreadsheet $spreadsheet, string $filename, User $user): StreamedResponse
    {
        $options = array_merge($this->getDefaultOptions(), $this->getUserOptions($user));

        return $this->getStreamedResponse(
            $this->getWriter($spreadsheet, $options),
            $filename
        );
    }
}
