<?php

namespace ForkCMS\Modules\Frontend\Domain\Google\TagManager;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

final class DataLayer extends ParameterBag
{
    public function generateHeadCode(): string
    {
        $code = [
            '<!-- Google Tag Manager Data Layer -->',
            '<script>',
            '  dataLayer = [%1$s];',
            '</script>',
            '<!-- End Google Tag Manager Data Layer -->',
        ];

        return sprintf(
            implode("\n", $code),
            !empty($this->all()) ? json_encode($this->all(), JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT) : ''
        );
    }

    public function generateNoScriptParameters(): string
    {
        if (empty($this->all())) {
            return '';
        }

        return '&' . http_build_query($this->all());
    }
}
