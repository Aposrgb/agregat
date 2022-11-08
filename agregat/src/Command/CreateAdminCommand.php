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
        $user->setFirstName($helper->ask($input, $output, $question));

        $question = new Question(
            '<question>Please type email</question>: ',
        );
        $user->setEmail($helper->ask($input, $output, $question));

        $question = new Question(
            '<question>Please type password</question>: ',
        );
        $user->setPassword($this->hasher->hashPassword($user, $helper->ask($input, $output, $question)));

        $this->userRepository->add($user, true);
        return Command::SUCCESS;
    }

}