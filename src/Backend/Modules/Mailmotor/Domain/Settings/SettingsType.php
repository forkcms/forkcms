<?php

namespace Backend\Modules\Mailmotor\Domain\Settings;

/*
 * This file is part of the Fork CMS Mailmotor Module from SIESQO.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Modules\Mailmotor\Domain\Settings\Command\SaveSettings;
use Common\Language;
use MailMotor\Bundle\MailMotorBundle\Manager\SubscriberGatewayManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SettingsType extends AbstractType
{
    /**
     * @var SubscriberGatewayManager
     */
    protected $subscriberGatewayManager;

    public function __construct(SubscriberGatewayManager $subscriberGatewayManager)
    {
        $this->subscriberGatewayManager = $subscriberGatewayManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $mailEngines = $this->getPossibleMailEngines();

        // if we have multiple templates, add a dropdown to select them
        if (count($mailEngines) > 0) {
            $builder->add(
                'mailEngine',
                ChoiceType::class,
                [
                    'required' => true,
                    'label' => 'lbl.MailEngine',
                    'choices' => $mailEngines,
                    'choice_translation_domain' => false,
                ]
            );
        }

        $builder->add(
            'apiKey',
            TextType::class,
            [
                'required' => true,
                'label' => 'lbl.ApiKey',
            ]
        )->add(
            'listId',
            TextType::class,
            [
                'required' => true,
                'label' => 'lbl.ListId',
            ]
        );

        $doubleOptIn = [
            'label' => 'msg.DoubleOptIn',
            'required' => false,
        ];
        $overwriteInterests = [
            'label' => 'msg.OverwriteInterests',
            'required' => false,
        ];

        $automaticallySubscribeFromFormBuilderSubmittedForm = [
            'label' => 'msg.AutomaticallySubscribeFromFormBuilderSubmittedForm',
            'required' => false,
        ];

        if (!array_key_exists('data', $options)) {
            $overwriteInterests['attr']['checked'] = 'checked';
            $doubleOptIn['attr']['checked'] = 'checked';
            $automaticallySubscribeFromFormBuilderSubmittedForm['attr']['checked'] = 'checked';
        }

        $builder->add(
            'doubleOptIn',
            CheckboxType::class,
            $doubleOptIn
        );

        $builder->add(
            'overwriteInterests',
            CheckboxType::class,
            $overwriteInterests
        );

        $builder->add(
            'automaticallySubscribeFromFormBuilderSubmittedForm',
            CheckboxType::class,
            $automaticallySubscribeFromFormBuilderSubmittedForm
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SaveSettings::class,
            'validation_groups' => function (FormInterface $form) {
                $data = $form->getData();
                if ($data->mailEngine === 'not_implemented') {
                    return ['Default'];
                }

                return ['Default', 'mail_engine_selected'];
            },
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'settings';
    }

    private function getPossibleMailEngines(): array
    {
        $ddmValuesForMailEngines = [];
        $mailEnginesWithSubscriberGateway = $this->subscriberGatewayManager->getAll();

        foreach ($mailEnginesWithSubscriberGateway as $key => $mailEngine) {
            $label = ucfirst(($key === 'not_implemented') ? Language::lbl('None') : $key);

            $ddmValuesForMailEngines[$label] = $key;
        }

        return $ddmValuesForMailEngines;
    }
}
