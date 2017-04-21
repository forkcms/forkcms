<?php

namespace ForkCMS\Bundle\InstallerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Builds the form to select languages to install
 */
class LanguagesType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'language_type',
                'choice',
                [
                    'expanded' => true,
                    'multiple' => false,
                    'choices' => [
                        'single' => 'Just one language',
                        'multiple' => 'Multiple languages',
                    ],
                ]
            )
            ->add(
                'languages',
                'choice',
                [
                    'choices' => $this->getInstallableLanguages(),
                    'expanded' => true,
                    'multiple' => true,
                ]
            )
            ->add(
                'default_language',
                'choice',
                [
                    'choices' => $this->getInstallableLanguages(),
                ]
            )
            ->add(
                'same_interface_language',
                'checkbox',
                [
                    'label' => 'Use the same language(s) for the CMS interface.',
                    'required' => false,
                ]
            )
            ->add(
                'default_interface_language',
                'choice',
                [
                    'choices' => $this->getInstallableLanguages(),
                ]
            )
            ->add(
                'interface_languages',
                'choice',
                [
                    'choices' => $this->getInstallableLanguages(),
                    'multiple' => true,
                    'expanded' => true,
                ]
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'ForkCMS\Bundle\InstallerBundle\Entity\InstallationData',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'install_languages';
    }

    /**
     * @return array
     */
    protected function getInstallableLanguages()
    {
        return [
            'en' => 'English',
            'zh' => 'Chinese',
            'nl' => 'Dutch',
            'fr' => 'French',
            'de' => 'German',
            'el' => 'Greek',
            'hu' => 'Hungarian',
            'it' => 'Italian',
            'lt' => 'Lithuanian',
            'ru' => 'Russian',
            'es' => 'Spanish',
            'sv' => 'Swedish',
            'uk' => 'Ukrainian',
        ];
    }
}
