<?php

namespace Backend\Modules\Pages\Domain\Page\Form;

use Backend\Core\Engine\Authentication;
use Backend\Form\EventListener\AddMetaSubscriber;
use Backend\Form\Type\TagsType;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\SingleMediaGroupType;
use Backend\Modules\Pages\Domain\Page\Command\CreatePage;
use Backend\Modules\Pages\Domain\Page\Page;
use Backend\Modules\Pages\Domain\Page\PageDataTransferObject;
use Backend\Modules\Pages\Domain\Page\PageRepository;
use Backend\Modules\Pages\Domain\Page\PageVersionDataGrid;
use Backend\Modules\Pages\Domain\Page\Status;
use Common\Form\TitleType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('title', TitleType::class);
        if ($this->authenticatedUserIsAllowedToSeeAndEditTags()) {
            $builder->add('tags', TagsType::class, ['label' => 'lbl.Tags', 'required' => false]);
        }
        $builder->add(
            'image',
            SingleMediaGroupType::class,
            [
                'label' => 'lbl.Image',
                'required' => false,
                'minimum_items' => 0,
            ]
        );
        $builder->addEventSubscriber(
            new AddMetaSubscriber(
                'Pages', // Virtual to make sure the correct url is used
                'Page', // Virtual to make sure the correct url is used
                PageRepository::class,
                'getUrl',
                [
                    'getData.getLocale',
                    'getData.getId',
                    'getData.getParentId',
                    'getData.isAction',
                ],
                'title',
                true
            )
        );
        $builder->add('navigation', PageNavigationType::class);
        $builder->add('data', PageDataType::class);
        $builder->add('settings', PageSettingsType::class);
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            static function (FormEvent $event): void {
                $event->getForm()->add(
                    'content',
                    PageContentType::class,
                    [
                        'selectedTemplateId' => $event->getData()->templateId,
                        'load_default_blocks' => !$event->getData()->page instanceof Page,
                    ]
                );

                if (!$event->getData() instanceof CreatePage) {
                    $event->getForm()->add(
                        'saveAsDraft',
                        SubmitType::class,
                        [
                            'label' => 'lbl.SaveDraft',
                        ]
                    );
                }
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => PageDataTransferObject::class,
            ]
        );
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        parent::finishView($view, $form, $options);

        if (!$form->getData()->hasExistingPage()) {
            return;
        }

        $page = $form->getData()->getPageEntity();
        $view->vars['dataGridDrafts'] = PageVersionDataGrid::getHtml($page, Status::draft());
        $view->vars['dataGridRevisions'] = PageVersionDataGrid::getHtml($page, Status::archive());
    }

    private function authenticatedUserIsAllowedToSeeAndEditTags(): bool
    {
        return Authentication::isAllowedAction('Edit', 'Tags') && Authentication::isAllowedAction('GetAllTags', 'Tags');
    }
}
