<?php

namespace App\Repository;

use App\Entity\Registration;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RegistrationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Registration::class);
    }

    public function findActiveForUser(User $user): array
    {
        return $this->createQueryBuilder('registration')
            ->join('registration.event', 'event')
            ->addSelect('event')
            ->andWhere('registration.user = :user')
            ->andWhere('registration.status != :cancelled')
            ->setParameter('user', $user)
            ->setParameter('cancelled', Registration::STATUS_CANCELLED)
            ->orderBy('registration.registeredAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
