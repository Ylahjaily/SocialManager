<?php

namespace App\Repository;

use App\Entity\ReviewComment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ReviewComment|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReviewComment|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReviewComment[]    findAll()
 * @method ReviewComment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReviewCommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReviewComment::class);
    }

    // /**
    //  * @return ReviewComment[] Returns an array of ReviewComment objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ReviewComment
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * @return ReviewComment[] Returns an array of ReviewComment objects
     */
    public function findReviewCommentsByReview($review)
    {
        return $this->createQueryBuilder('reviewComment')
            ->select('reviewComment')
            ->setParameter('review', $review)
            ->where( 'reviewComment.review_id = :review')
            ->getQuery()
            ->getResult()
            ;
    }
}
