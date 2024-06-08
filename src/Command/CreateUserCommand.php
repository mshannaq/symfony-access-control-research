<?php
//src/Command/CreateUserCommand.php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-user',
    description: 'Creates a new user.',
    hidden: false,
    aliases: ['app:add-user']
)]
class CreateUserCommand extends Command
{
    private $passwordHasher;
    private $entityManager;

    public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager)
    {
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Creates a new user')
            ->setHelp('This command allows you to create a new user.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        // Ask for username
        $question = new Question('Enter User username: ');
        $username = $helper->ask($input, $output, $question);

        // Ask for password
        $question = new Question('Enter User password: ');
        $question->setHidden(true);
        $password = $helper->ask($input, $output, $question);


        // Create new user entity
        $user = new User();
        $user->setUsername($username);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));

        //add user role as client by default
        $user->setRoles(['ROLE_CLIENT']);

        // Persist user to the database
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln('User Account created successfully.');

        return Command::SUCCESS;
    }
}
