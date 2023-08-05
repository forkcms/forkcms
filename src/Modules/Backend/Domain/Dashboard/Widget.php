<?php

namespace ForkCMS\Modules\Backend\Domain\Dashboard;

use Symfony\Contracts\Translation\TranslatableInterface;

final class Widget
{
    public function __construct(
        private TranslatableInterface|string $moduleLabel,
        private TranslatableInterface|string $widgetLabel,
        private string $content
    ) {
    }

    public function getModuleLabel(): TranslatableInterface|string
    {
        return $this->moduleLabel;
    }

    public function getWidgetLabel(): TranslatableInterface|string
    {
        return $this->widgetLabel;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
