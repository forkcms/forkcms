<?php

namespace ForkCMS\Bundle\InstallerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Builds the form to select languages to install
 *
 * @author Wouter Sioen <wouter.sioen@wijs.be>
 */
class LanguagesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'language_type',
                'choice',
                array(
                    'expanded' => true,
                    'multiple' => false,
                    'choices'  => array(
                        'single'   => 'Just one language',
                        'multiple' => 'Multiple languages',
                    ),
                    'data' => 'single',
                )
            )
            ->add(
                'languages',
                'choice',
                array(
                    'choices'  => $this->getInstallableLanguages(),
                    'expanded' => true,
                    'multiple' => true,
                )
            )
            ->add(
                'default_language',
                'choice',
                array(
                    'choices' => $this->getInstallableLanguages(),
                )
            )
            ->add(
                'same_interface_language',
                'checkbox',
                array(
                    'attr' => array('checked' => 'checked'),
                    'label' => 'Use the same language(s) for the CMS interface.',
                )
            )
            ->add(
                'interface_language',
                'choice',
                array(
                    'choices' => $this->getInstallableLanguages(),
                )
            )
            ->add(
                'interface_languages',
                'choice',
                array(
                    'choices'  => $this->getInstallableLanguages(),
                    'multiple' => true,
                    'expanded' => true,
                )
            )
        ;
    }

    public function getName()
    {
        return 'install_languages';
    }

    protected function getInstallableLanguages()
    {
        return array(
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
            'uk' => 'Ukrainian'
        );
    }
}
