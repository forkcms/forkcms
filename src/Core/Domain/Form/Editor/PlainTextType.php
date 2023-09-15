<?php

namespace ForkCMS\Core\Domain\Form\Editor;

use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

final class PlainTextType extends AbstractType implements EditorTypeImplementationInterface
{
    public function getParent(): string
    {
        return TextareaType::class;
    }

    public function getLabel(): TranslationKey
    {
        return TranslationKey::label('Text');
    }

    public function parseContent(string $content): string
    {
        return $content;
    }

    public function getBlockPrefix(): string
    {
        return 'plain_text_editor';
    }
}
