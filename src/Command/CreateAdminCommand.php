<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Admin;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Creates a new admin.',
    hidden: false,
    aliases: ['app:add-admin']
)]
class CreateAdminCommand extends Command
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
        $this->setDescription('Creates a new admin')
            ->setHelp('This command allows you to create a new admin.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        // Ask for username
        $question = new Question('Enter username: ');
        $username = $helper->ask($input, $output, $question);

        // Ask for password
        $question = new Question('Enter password: ');
        $question->setHidden(true);
        $password = $helper->ask($input, $output, $question);

        // Ask if the user should be an admin
        $question = new Question('Make this user an admin? (yes/no): ', 'no');
        $isAdmin = strtolower($helper->ask($input, $output, $question)) === 'yes';

        // Create new user entity
        $user = new Admin();
        $user->setUsername($username);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));

        // Set roles based on the user's response
        if ($isAdmin) {
            $user->setRoles(['ROLE_ADMIN']);
        } else {
            $user->setRoles([]);
        }

        // Persist user to the database
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln('Admin User created successfully.');

        return Command::SUCCESS;
    }
}
