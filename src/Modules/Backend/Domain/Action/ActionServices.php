<?php

namespace ForkCMS\Modules\Backend\Domain\Action;

use Doctrine\ORM\EntityManagerInterface;
use ForkCMS\Core\Domain\Header\Header;
use Pageon\DoctrineDataGridBundle\DataGrid\DataGridFactory;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

final class ActionServices
{
    public function __construct(
        public readonly DataGridFactory $dataGridFactory,
        public readonly EntityManagerInterface $entityManager,
        public readonly Environment $twig,
        public readonly TranslatorInterface $translator,
        public readonly Header $header,
        public readonly RouterInterface $router,
        public readonly FormFactoryInterface $formFactory,
        public readonly MessageBusInterface $commandBus,
        public readonly AuthorizationCheckerInterface $authorizationChecker,
    ) {
    }
}
