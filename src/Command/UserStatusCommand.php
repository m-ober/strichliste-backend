<?php

namespace App\Command;

use App\Entity\User;
use App\Exception\UserNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UserStatusCommand extends Command {

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(string $name = null, EntityManagerInterface $entityManager) {
        parent::__construct($name);
        $this->entityManager = $entityManager;
    }

    protected function configure() {
        $this
            ->setName('app:user:status')
            ->setDescription('Updates User status')
            ->addArgument('userId', InputArgument::REQUIRED, 'Id or name')
            ->addArgument('active', InputArgument::REQUIRED, 'true or false');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $userId = $input->getArgument('userId');
        $active = $input->getArgument('active') === 'true';

        $user = $this->entityManager->getRepository(User::class)->findByIdentifier($userId);
        if (!$user) {
            throw new UserNotFoundException($user);
        }

        $user->setActive($active);

        if ($active) {
            $output->writeln(sprintf("User '%s' (%d) has been activated", $user->getName(), $user->getId()));
        } else {
            $output->writeln(sprintf("User '%s' (%d) has been deactivated", $user->getName(), $user->getId()));
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}