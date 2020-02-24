<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateAdminCommand extends Command
{
    protected static $defaultName = 'app:create-admin';
    private $entityManager;
    private $encoder;

    public function __construct(EntityManagerInterface $entityManager,UserPasswordEncoderInterface $encoder)
    {
        $this->entityManager = $entityManager;
        $this->encoder=$encoder;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('email', InputArgument::REQUIRED, 'email description')
            ->addArgument('apiKey', InputArgument::REQUIRED, 'apiKey description')
            ->addArgument('lastName', InputArgument::REQUIRED, 'lastName description')
            ->addArgument('firstName', InputArgument::REQUIRED, 'firstName description')
            ->addArgument('password', InputArgument::REQUIRED, 'password description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $apiKey = $input->getArgument('apiKey');
        $lastName = $input->getArgument('lastName');
        $firstName = $input->getArgument('firstName');
        $password = $input->getArgument('password');
        $io->note(sprintf('Create a User for email: %s', $email));

        $user = new User();
        $user->setEmail($email);
        $user->setApiKey($apiKey);
        $user->setLastName($lastName);
        $user->setFirstName($firstName);
        $user->setPassword($password);
        $user->setRoles(['ROLE_USER','ROLE_ADMIN']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success(sprintf('You have created a User with email: %s', $email));
    }
}
