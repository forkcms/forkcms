<?php

namespace ForkCMS\Modules\BlockEditor\Domain\Editor;

use ForkCMS\Core\Domain\Application\Application;
use ForkCMS\Core\Domain\Form\Editor\EditorTypeImplementationInterface;
use ForkCMS\Core\Domain\Header\Asset\Asset;
use ForkCMS\Core\Domain\Header\Header;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;
use RuntimeException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This will automatically load the form type that was selected in the frontend settings
 */
final class BlockEditorType extends AbstractType implements EditorTypeImplementationInterface
{
    public function __construct(
        private readonly BlockEditorConfig $blockEditorConfig,
        private readonly Header $header
    ) {
    }

    public function getParent(): string
    {
        return TextareaType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'block_editor';
    }

    public function getLabel(): TranslationKey
    {
        return TranslationKey::label('BlockEditor');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('blockEditorConfig', null);
    }

    public function parseContent(string $content): string
    {
        return $this->blockEditorConfig->createHtmlFromJson($content);
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        $blockEditorConfig = $options['blockEditorConfig'] ?? $this->blockEditorConfig;
        if (!$blockEditorConfig instanceof BlockEditorConfig) {
            throw new RuntimeException('Invalid block editor config');
        }

        $view->vars['attr']['data-fork-block-editor-config'] = json_encode(
            $blockEditorConfig->getConfig(),
            JSON_THROW_ON_ERROR
        );

        $this->header->addJs(
            Asset::forModule(
                Application::BACKEND,
                ModuleName::fromFQCN(self::class),
                'js/BlockEditor.js'
            )
        );
        foreach ($blockEditorConfig->getJavascripts() as $asset) {
            $this->header->addJs($asset);
        }
    }
}
