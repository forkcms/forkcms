<?php

namespace Backend\Form\Type;

use Backend\Core\Engine\Header;
use Backend\Core\Engine\Model;
use Backend\Core\Language\Language;
use Common\Core\Header\Priority;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditorType extends TextareaType
{
    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        switch (Model::getPreferredEditor()) {
            case 'ck-editor':
                $this->configureCkEditorOptions($optionsResolver);

                break;
            case 'block-editor':
                $this->configureBlockEditorOptions($optionsResolver);

                break;
            default:
                parent::configureOptions($optionsResolver);

                break;
        }
    }

    public function configureBlockEditorOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(['attr' => ['class' => 'inputBlockEditor']]);

        if (!Model::has('header')) {
            return;
        }
        /** @var Header $header */
        $header = Model::get('header');

        $header->addJS(
            '/js/editor.js',
            null,
            false,
            true,
            true,
            Priority::core()
        );
    }

    public function configureCkEditorOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(['attr' => ['class' => 'inputEditor']]);

        if (!Model::has('header')) {
            return;
        }
        // add the needed javascript to the header;
        $header = Model::get('header');
        // we add JS because we need CKEditor
        $header->addJS('ckeditor/ckeditor.js', 'Core', false);
        $header->addJS('ckeditor/adapters/jquery.js', 'Core', false);
        $header->addJS('ckfinder/ckfinder.js', 'Core', false);

        // add the internal link lists-file
        if (is_file(FRONTEND_CACHE_PATH . '/Navigation/editor_link_list_' . Language::getWorkingLanguage() . '.js')) {
            $timestamp = @filemtime(
                FRONTEND_CACHE_PATH . '/Navigation/editor_link_list_' . Language::getWorkingLanguage() . '.js'
            );
            $header->addJS(
                '/src/Frontend/Cache/Navigation/editor_link_list_' . Language::getWorkingLanguage(
                ) . '.js?m=' . $timestamp,
                null,
                false,
                true,
                false
            );
        }
    }

    public function getParent(): string
    {
        return TextareaType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'editor';
    }
}
