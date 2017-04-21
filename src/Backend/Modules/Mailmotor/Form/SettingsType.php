<?php

namespace Backend\Modules\Mailmotor\Form;

/*
 * This file is part of the Fork CMS Mailmotor Module from SIESQO.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Modules\Mailmotor\Command\SaveSettings;
use Common\Language;
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
     * @var array
     */
    protected $serviceIds;

    /**
     * SettingsType constructor.
     *
     * @param array $serviceIds
     */
    public function __construct(array $serviceIds)
    {
        $this->serviceIds = $serviceIds;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
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

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SaveSettings::class,
            'validation_groups' => function (FormInterface $form) {
                $data = $form->getData();
                if ($data->mailEngine != 'not_implemented') {
                    return ['Default', 'mail_engine_selected'];
                } else {
                    return ['Default'];
                }
            },
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'settings';
    }

    /**
     * Get mail engines.
     *
     * @return array
     */
    private function getPossibleMailEngines()
    {
        // init dropdown values
        $ddmValuesForMailEngines = [];

        // Add empty one
        $ddmValuesForMailEngines['not_implemented'] = ucfirst(Language::lbl('None'));

        // loop all container services to find "mail-engine" gateway services
        foreach ($this->serviceIds as $serviceId) {
            // the pattern to find mail engines
            $pattern = '/^mailmotor.(?P<mailengine>\w+).subscriber.gateway/';
            $matches = [];

            // we found a mail-engine gateway service
            if (preg_match($pattern, $serviceId, $matches)) {
                // we skip the fallback gateway
                if ($matches['mailengine'] == 'not_implemented') {
                    continue;
                }

                // add mailengine to dropdown values
                $ddmValuesForMailEngines[$matches['mailengine']] = ucfirst($matches['mailengine']);
            }
        }

        return $ddmValuesForMailEngines;
    }
}
