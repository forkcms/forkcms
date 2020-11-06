<?php

namespace Common\Form;

use Backend\Core\Engine\Header as BackendHeader;
use Common\Core\Header\Priority;
use Frontend\Core\Header\Header as FrontendHeader;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DatePickerType extends DateType
{
    /**
     * @var BackendHeader|FrontendHeader
     */
    private $header;

    public function __construct(ContainerInterface $container)
    {
        $this->header = $container->get('header');
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefault('widget', 'single_text');
        $resolver->setDefault('html5', false);
        $resolver->setDefault('format', 'dd/MM/yyyy');
        $resolver->setDefault(
            'attr',
            [
                'data-role' => 'fork-datepicker',
            ]
        );
    }
}
