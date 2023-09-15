<?php

namespace ForkCMS\Core\Domain\Form;

use ForkCMS\Core\Domain\Form\Editor\EditorTypeImplementationInterface;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleSettings;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/** This will automatically load the form type that was selected in the frontend settings */
class EditorType extends AbstractType
{
    public const SETTING_NAME = 'editor';

    /** @param ServiceLocator<EditorTypeImplementationInterface> $editorTypeImplementations */
    public function __construct(
        private readonly ModuleSettings $moduleSettings,
        private readonly ServiceLocator $editorTypeImplementations,
    ) {
    }

    public function getParent(): string
    {
        return $this->moduleSettings->get(
            ModuleName::core(),
            self::SETTING_NAME,
            TextareaType::class
        );
    }

    public function parseContent(string $content): string
    {
        $editorClassName = $this->moduleSettings->get(ModuleName::core(), self::SETTING_NAME);

        if ($this->editorTypeImplementations->has($editorClassName)) {
            return $this->editorTypeImplementations->get($editorClassName)->parseContent($content);
        }

        return $content;
    }
}
