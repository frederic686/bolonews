<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function findBySearch(?string $query): array
    {
        $qb = $this->createQueryBuilder('a')
            ->andWhere('a.publie = true');

        if (!empty($query) && is_string($query)) {
            $qb->andWhere('a.titre LIKE :q OR a.chapeau LIKE :q')
               ->setParameter('q', '%' . $query . '%');
        }

        return $qb->orderBy('a.dateCreation', 'DESC')
                  ->getQuery()
                  ->getResult();
    }
}
