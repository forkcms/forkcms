<?php

namespace Backend\Modules\ContentBlocks\Form;

use Backend\Form\Type\EditorType;
use Backend\Modules\ContentBlocks\Command\CreateContentBlock;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentBlockType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'title',
            TextType::class,
            [
                'required' => true,
                'label' => 'lbl.Title',
            ]
        )->add(
            'text',
            EditorType::class,
            [
                'required' => true,
                'label' => 'lbl.Content',
            ]
        );

        $templates = $this->getPossibleTemplates($options['theme']);
        // if we have multiple templates, add a dropdown to select them
        if (count($templates) > 1) {
            $builder->add(
                'template',
                ChoiceType::class,
                [
                    'required' => true,
                    'label' => 'lbl.Template',
                    'choices' => $templates,
                    'choice_translation_domain' => false,
                ]
            );
        }


        $isVisibleOptions = [
            'label' => 'lbl.VisibleOnSite',
            'required' => false,
        ];

        if (!array_key_exists('data', $options)) {
            $isVisibleOptions['attr']['checked'] = 'checked';
        }

        $builder->add(
            'isVisible',
            CheckboxType::class,
            $isVisibleOptions
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('theme');
        $resolver->setDefaults(['data_class' => CreateContentBlock::class]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'content_block';
    }

    /**
     * Get templates.
     *
     * @return array
     */
    private function getPossibleTemplates(string $theme)
    {
        $templates = [];
        $finder = new Finder();
        $finder->name('*.html.twig');
        $finder->in(FRONTEND_MODULES_PATH . '/ContentBlocks/Layout/Widgets');

        // if there is a custom theme we should include the templates there also
        if ($theme !== 'Core') {
            $path = FRONTEND_PATH . '/Themes/' . $theme . '/Modules/ContentBlocks/Layout/Widgets';
            if (is_dir($path)) {
                $finder->in($path);
            }
        }

        foreach ($finder->files() as $file) {
            $templates[] = $file->getBasename();
        }

        $templates = array_unique($templates);

        return array_combine($templates, $templates);
    }
}
