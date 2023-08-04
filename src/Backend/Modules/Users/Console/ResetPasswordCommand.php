<?php

namespace Backend\Modules\Users\Console;

use Backend\Modules\Users\Engine\Model as BackendUsersModel;
use Backend\Core\Engine\User as BackendUser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class ResetPasswordCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('forkcms:users:reset-password')
            ->setDescription('Reset a users password')
            ->addArgument('email', InputArgument::OPTIONAL, '(Optional) The users email-address')
            ->addArgument('password', InputArgument::OPTIONAL, '(Optional) The desired new password');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $helper = $this->getHelper('question');

        $questionEmailAddress = new Question('Please provide the users email-address: ');
        $questionEmailAddressAnswer = $input->getArgument('email');

        if ($questionEmailAddressAnswer === null) {
            $questionEmailAddressAnswer = $helper->ask($input, $output, $questionEmailAddress);
        }

        $subjectUserId = BackendUsersModel::getIdByEmail(
            $questionEmailAddressAnswer
        );

        if ($subjectUserId === false) {
            $output->writeln(
                sprintf('<error>User not found with email-address "%s".</error>', $questionEmailAddressAnswer)
            );

            return;
        }

        $questionPassword = new Question('Please provide the users new desired password: ');
        $questionPassword->setHidden(true);
        $questionPasswordAnswer = $input->getArgument('password');

        if ($questionPasswordAnswer === null) {
            $questionPasswordAnswer = $helper->ask($input, $output, $questionPassword);
        }

        BackendUsersModel::updatePassword(
            new BackendUser($subjectUserId, $questionEmailAddressAnswer),
            $questionPasswordAnswer
        );

        $output->writeln('Password has been updated');
    }
}
