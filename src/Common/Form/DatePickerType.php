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

        if ($this->header instanceof BackendHeader) {
            $this->header->addJS('/js/vendors/bootstrap-datepicker.min.js', 'Core', false, true, true, Priority::core());
            $this->header->addCSS('/css/vendors/bootstrap-datepicker3.standalone.min.css', 'Core', true, true, true, Priority::core());

            return;
        }

        $this->header->addJS('/js/vendors/bootstrap-datepicker.min.js', false, true, Priority::core());
        $this->header->addCSS('/css/vendors/bootstrap-datepicker3.standalone.min.css', true, true, Priority::core());
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
