<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

//    /**
//     * @return Article[] Returns an array of Article objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Article
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function searchByUser($user, $query = null, $categorie = null, $publie = null, bool $isPublished = true)
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.categorie', 'c')
            ->addSelect('c')
            ->andWhere('a.auteur = :user')
            ->setParameter('user', $user)
            ->andWhere('a.publie = :publie')
            ->setParameter('publie', $isPublished)
            ->orderBy('a.dateCreation', 'DESC');

        if ($query) {
            $qb->andWhere('a.titre LIKE :q OR a.chapeau LIKE :q')
            ->setParameter('q', '%' . $query . '%');
        }
        return $qb->getQuery()->getResult();
    }

}
