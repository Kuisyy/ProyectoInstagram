<?php

namespace App\Repository;

use App\Entity\Publicacion;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Publicacion>
 */
class PublicacionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Publicacion::class);
    }

    
    /**
     * @return Publicacion[]
     */
    public function findPublicacionesFromFollowed(User $user): array
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.userPost', 'u')
            ->innerJoin('u.followers', 'f')
            ->where('f.follower = :user')
            ->setParameter('user', $user)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Publicacion[] Returns an array of Publicacion objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Publicacion
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findTopLikedPosts(): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.likes', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }

    public function findTopCommentedPosts(): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.comments', 'c')
            ->groupBy('p.id')
            ->orderBy('COUNT(c.id)', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }
}
