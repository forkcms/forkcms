<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaGroup;

use Ramsey\Uuid\Uuid;
use SimpleBus\Message\Bus\Middleware\MessageBusSupportingMiddleware;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Backend\Core\Engine\Model;
use Backend\Core\Language\Language;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\Command\CreateMediaGroup;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\Command\UpdateMediaGroup;

class MediaGroupType extends AbstractType
{
    /** @var MessageBusSupportingMiddleware */
    private $commandBus;

    /** @var MediaGroup[] */
    private $mediaGroups;

    /** @var MediaGroupRepository */
    private $mediaGroupRepository;

    /**
     * MediaGroupType constructor.
     *
     * @param MediaGroupRepository $mediaGroupRepository
     * @param $commandBus
     */
    public function __construct(
        MediaGroupRepository $mediaGroupRepository,
        MessageBusSupportingMiddleware $commandBus
    ) {
        $this->mediaGroupRepository = $mediaGroupRepository;
        $this->commandBus = $commandBus;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'mediaIds',
                HiddenType::class,
                [
                    'attr' => [
                        'class' => 'mediaIds',
                    ],
                ]
            )
            ->add(
                'type',
                HiddenType::class,
                [
                    'attr' => [
                        'class' => 'type',
                    ],
                ]
            )
            ->addModelTransformer(
                new CallbackTransformer(
                    $this->getMediaGroupTransformFunction(),
                    $this->getMediaGroupReverseTransformFunction()
                )
            );

        self::parseFiles();
    }

    public static function parseFiles()
    {
        // Currently Fork CMS can't load in the dependency "@header", since it is defined later when loading in
        // Thats why we still use a static function to get the header
        $header = Model::get('header');

        // Add "fine-uploader" css/js
        $header->addCSS('/css/vendors/fine-uploader/fine-uploader-new.min.css', null, true, false);
        $header->addJS('/js/vendors/jquery.fine-uploader.min.js', null, false, true);

        $header->addCSS('MediaLibrary.css', 'MediaLibrary', false, true);
        $header->addJS('MediaLibraryAddFolder.js', 'MediaLibrary', true);
        $header->addJS('MediaLibraryHelper.js', 'MediaLibrary', true);
    }

    /**
     * @return \Closure
     */
    private function getMediaGroupTransformFunction()
    {
        return function ($mediaGroup) {
            if (!$mediaGroup instanceof MediaGroup) {
                return true;
            }

            $this->mediaGroups[(string) $mediaGroup->getId()] = $mediaGroup;

            return [
                'id' => (string) $mediaGroup->getId(),
                'type' => $mediaGroup->getType(),
                'mediaIds' => implode(',', $mediaGroup->getIdsForConnectedItems()),
            ];
        };
    }

    /**
     * @return \Closure
     */
    private function getMediaGroupReverseTransformFunction()
    {
        /**
         * @param $mediaGroupData
         * @return MediaGroup|null
         */
        return function ($mediaGroupData) {
            /** @var string $mediaGroupId */
            $mediaGroupId = $mediaGroupData['id'];

            /** @var Type $mediaGroupType */
            $mediaGroupType = Type::fromString($mediaGroupData['type']);

            /** @var MediaGroup $mediaGroup */
            $mediaGroup = $this->getMediaGroup($mediaGroupId, $mediaGroupType);

            /** @var array $mediaItemIds */
            $mediaItemIds = ($mediaGroupData['mediaIds'] !== null)
                ? explode(',', trim($mediaGroupData['mediaIds'])) : array();

            /** @var UpdateMediaGroup $updateMediaGroup */
            $updateMediaGroup = new UpdateMediaGroup(
                $mediaGroup,
                $mediaItemIds
            );

            // Handle the MediaGroup update
            $this->commandBus->handle($updateMediaGroup);

            return $updateMediaGroup->getMediaGroupEntity();
        };
    }

    /**
     * @param string $mediaGroupId
     * @param Type $mediaGroupType
     * @return MediaGroup
     */
    private function getMediaGroup(string $mediaGroupId, Type $mediaGroupType)
    {
        try {
            /** @var MediaGroup|null $mediaGroup */
            return $this->mediaGroupRepository->getOneById($mediaGroupId);
        } catch (\Exception $e) {
            /** @var CreateMediaGroup $createMediaGroup */
            $createMediaGroup = new CreateMediaGroup(
                $mediaGroupType,
                Uuid::fromString($mediaGroupId)
            );

            // Handle the MediaGroup create
            $this->commandBus->handle($createMediaGroup);

            return $createMediaGroup->getMediaGroupEntity();
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(
            [
                'label',
            ]
        );
        $resolver->setDefaults(
            [
                'label' => Language::lbl('ConnectedMedia'),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'media_group';
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if ($view->parent === null) {
            throw new LogicException(
                'The MediaGroupType is not a stand alone type, it needs to be used in a parent form'
            );
        }

        $view->vars['label'] = $options['label'];
    }
}
