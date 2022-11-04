<?php

namespace App\Command;

use App\Entity\User;
use App\Helper\EnumRoles\UserRoles;
use App\Helper\EnumStatus\UserStatus;
use App\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateAdminCommand extends Command
{
    public function __construct(
        protected UserRepository $userRepository,
        protected UserPasswordHasherInterface $hasher
    )
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('create:user:admin')
            ->setDescription('Create default admin')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user = (new User())
            ->setRoles([UserRoles::ROLE_ADMIN->value])
            ->setStatus(UserStatus::CONFIRMED->value)
        ;

        $helper = $this->getHelper('question');

        $question = new Question(
            '<question>Please type username</question>: ',
        );
        $user->setFirstname($helper->ask($input, $output, $question));

        $question = new Question(
            '<question>Please type surname</question>: ',
        );
        $user->setSurname($helper->ask($input, $output, $question));

        $question = new Question(
            '<question>Please type country</question>: ',
        );
        $user->setCountry($helper->ask($input, $output, $question));

        $question = new Question(
            '<question>Please type phone</question>: ',
        );
        $user->setPhone($helper->ask($input, $output, $question));

        $question = new Question(
            '<question>Please type email</question>: ',
        );
        $user->setEmailAdminPanel($helper->ask($input, $output, $question));

        $question = new Question(
            '<question>Please type password</question>: ',
        );
        $password = $helper->ask($input, $output, $question);
        $user->setAdminPanelPassword($this->hasher->hashPassword($user, $password));

        $this->userRepository->add($user, true);
        return Command::SUCCESS;
    }

}