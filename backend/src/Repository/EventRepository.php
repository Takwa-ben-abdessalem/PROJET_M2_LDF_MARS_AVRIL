<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function findPublished(): array
    {
        return $this->createQueryBuilder('event')
            ->andWhere('event.isPublished = :published')
            ->setParameter('published', true)
            ->orderBy('event.eventDate', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
