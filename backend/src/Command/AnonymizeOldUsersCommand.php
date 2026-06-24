<?php

namespace App\Command;

use App\Entity\User;
use App\Service\UserAnonymizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:anonymize-old-users', description: 'Anonymise les comptes inactifs depuis plus de 2 ans.')]
class AnonymizeOldUsersCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserAnonymizer $anonymizer,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $threshold = new \DateTimeImmutable('-2 years');
        $users = $this->entityManager->getRepository(User::class)->createQueryBuilder('user')
            ->andWhere('((user.lastActivityAt IS NULL AND user.createdAt < :threshold) OR user.lastActivityAt < :threshold)')
            ->andWhere('user.isAnonymized = false')
            ->setParameter('threshold', $threshold)
            ->getQuery()
            ->getResult();

        foreach ($users as $user) {
            $this->anonymizer->anonymize($user);
        }

        $this->entityManager->flush();
        $output->writeln(sprintf('%d utilisateur(s) anonymise(s).', count($users)));

        return Command::SUCCESS;
    }
}
