<?php

namespace ForkCMS\Modules\Extensions\Domain\ThemeTemplate;

use Assert\Assertion;
use InvalidArgumentException;
use Twig\Environment;

final class TemplatePreviewGenerator
{
    public function __construct(private Environment $twig)
    {
    }

    public function generatePreview(ThemeTemplate $template): void
    {
        $settings = $template->getSettings();
        if (!$settings->hasChanges()) {
            return;
        }

        $settings->set('html', $this->buildTemplateHTML($settings->get('layout')));
        $settings->set('htmlLarge', $this->buildTemplateHTML($settings->get('layout'), true));
    }

    private function buildTemplateHTML(string $format, bool $includeFormElements = false): string
    {
        // cleanup
        $table = self::templateSyntaxToArray($format);

        $rows = count($table);
        if ($rows === 0) {
            throw new InvalidArgumentException('Invalid template-format.');
        }

        $cells = count($table[0]);
        $htmlContent = [];
        for ($y = 0; $y < $rows; ++$y) {
            $htmlContent[$y] = [];

            // loop cells
            for ($x = 0; $x < $cells; ++$x) {
                // skip if needed
                if (!isset($table[$y][$x])) {
                    continue;
                }

                $value = $table[$y][$x];
                $colspan = 1;
                // reset items in the same column
                while ($x + $colspan < $cells && $table[$y][$x + $colspan] === $value) {
                    $table[$y][$x + $colspan++] = null;
                }

                $rowspan = 1;
                $rowMatches = true;

                // loop while the rows match
                while ($rowMatches && $y + $rowspan < $rows) {
                    // loop columns inside spanned columns
                    for ($i = 0; $i < $colspan; ++$i) {
                        // check value
                        if ($table[$y + $rowspan][$x + $i] !== $value) {
                            // no match, so stop
                            $rowMatches = false;
                            break;
                        }
                    }

                    // any rowmatches?
                    if ($rowMatches) {
                        // loop columns and reset value
                        for ($i = 0; $i < $colspan; ++$i) {
                            $table[$y + $rowspan][$x + $i] = null;
                        }

                        // increment
                        ++$rowspan;
                    }
                }

                $htmlContent[$y][$x] = [
                    'title' => ucfirst($value),
                    'value' => $value,
                    'exists' => $value !== '/',
                    'rowspan' => $rowspan,
                    'colspan' => $colspan,
                    'includeFormElements' => $includeFormElements,
                ];
            }
        }

        return $this->twig->render(
            '@Extensions/_template_preview.html.twig',
            ['table' => $htmlContent]
        );
    }

    private static function templateSyntaxToArray(string $syntax): array
    {
        $syntax = trim(str_replace(["\n", "\r", ' '], '', $syntax));
        $table = [];

        // check template settings format
        if (!self::isValidTemplateSyntaxFormat($syntax)) {
            return $table;
        }

        // split into rows
        $rows = explode('],[', $syntax);

        foreach ($rows as $i => $row) {
            $row = trim(str_replace(['[', ']'], '', $row));
            $table[$i] = explode(',', $row);
        }

        if (!array_key_exists(0, $table)) {
            return [];
        }

        $columns = count($table[0]);

        foreach ($table as $row) {
            if (count($row) !== $columns) {
                return [];
            }
        }

        return $table;
    }

    private static function isValidTemplateSyntaxFormat(string $syntax): bool
    {
        return Assertion::regex(
            $syntax,
            '/^\[(\/|[a-z\d])+(,(\/|[a-z\d]+))*](,\[(\/|[a-z\d])+(,(\/|[a-z\d]+))*])*$/i'
        );
    }
}
