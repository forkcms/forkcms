<?php

namespace ForkCMS\Modules\Backend\Console;

use ForkCMS\Modules\Backend\Domain\User\Command\ChangeUser;
use ForkCMS\Modules\Backend\Domain\User\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

class ResetUserPasswordCommand extends Command
{
    private SymfonyStyle $formatter;

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly MessageBusInterface $commandBus
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('forkcms:backend:reset-user-password')
            ->setDescription('Reset a users password')
            ->addArgument('email', InputArgument::OPTIONAL, '(Optional) The users email-address')
            ->addArgument('password', InputArgument::OPTIONAL, '(Optional) The desired new password');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->formatter = new SymfonyStyle($input, $output);

        return $this->changePassword($input->getArgument('email'), $input->getArgument('password'));
    }

    private function changePassword(?string $email, ?string $password): int
    {
        $email = $email ?? $this->formatter->ask('Please provide the users email-address: ');

        $user = $this->userRepository->findOneBy(['email' => $email]);

        if ($user === null) {
            $this->formatter->error(sprintf('There is no user with email-address "%s".', $email));

            return self::INVALID;
        }

        $password = $password ?? $this->formatter->askHidden('Please provide the users new desired password: ');
        $changeUser = new ChangeUser($user);
        $changeUser->plainTextPassword = $password;

        $this->commandBus->dispatch($changeUser);

        $this->formatter->success(sprintf('Password has been updated for user "%s".', $email));

        return self::SUCCESS;
    }
}
