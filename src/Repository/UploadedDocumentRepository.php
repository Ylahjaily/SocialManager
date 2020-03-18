<?php

namespace App\Repository;

use App\Entity\UploadedDocument;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method UploadedDocument|null find($id, $lockMode = null, $lockVersion = null)
 * @method UploadedDocument|null findOneBy(array $criteria, array $orderBy = null)
 * @method UploadedDocument[]    findAll()
 * @method UploadedDocument[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UploadedDocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UploadedDocument::class);
    }

    // /**
    //  * @return UploadedDocument[] Returns an array of UploadedDocument objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UploadedDocument
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
      * @return UploadedDocument[] Returns an array of UploadedDocument objects
     */
    public function findApprovedFiles()
    {
        return $this->createQueryBuilder('uploadedDocument')
            ->innerJoin('uploadedDocument.reviews','reviews')
            ->addSelect('reviews')
            ->where('reviews.is_approved = true')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return UploadedDocument[] Returns an array of UploadedDocument objects
     */
    public function findUnProcessedFiles()
    {
        return $this->createQueryBuilder('uploadedDoc')
            ->select('uploadedDoc')
            ->where( 'SIZE(uploadedDoc.reviews) = 0')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return UploadedDocument[] Returns an array of UploadedDocument objects
     */
    public function findApprovedFilesByReviewer($user)
    {
        return $this->createQueryBuilder('uploadedDoc')
            ->innerJoin('uploadedDoc.reviews','reviews')
            ->addSelect('reviews')
            ->setParameter('user', $user)
            ->where('reviews.is_approved = true')
            ->andWhere('reviews.user_id = :user')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return UploadedDocument[] Returns an array of UploadedDocument objects
     */
    public function findRejectedFilesByReviewer($user)
    {
        return $this->createQueryBuilder('uploadedDoc')
            ->innerJoin('uploadedDoc.reviews','reviews')
            ->addSelect('reviews')
            ->setParameter('user', $user)
            ->where('reviews.is_approved = false')
            ->andWhere('reviews.user_id = :user')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return UploadedDocument[] Returns an array of UploadedDocument objects
     */
    public function findRejectedFilesByMember($user)
    {
        return $this->createQueryBuilder('uploadedDoc')
            ->innerJoin('uploadedDoc.reviews','reviews')
            ->addSelect('reviews')
            ->setParameter('user', $user)
            ->where('reviews.is_approved = false')
            ->andWhere('uploadedDoc.user_id = :user')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return UploadedDocument[] Returns an array of UploadedDocument objects
     */
    public function findPublishedFiles()
    {
        return $this->createQueryBuilder('uploadedDoc')
            ->select('uploadedDoc')
            ->where('uploadedDoc.is_published = true')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return UploadedDocument[] Returns an array of UploadedDocument objects
     */
    public function findPublishedFilesByUser($user)
    {
        return $this->createQueryBuilder('uploadedDoc')
            ->select('uploadedDoc')
            ->setParameter('user', $user)
            ->where('uploadedDoc.is_published = true')
            ->andWhere('uploadedDoc.user_id = :user')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return UploadedDocument[] Returns an array of UploadedDocument objects
     */
    public function findApprovedFilesByMember($user)
    {
        return $this->createQueryBuilder('uploadedDoc')
            ->innerJoin('uploadedDoc.reviews','reviews')
            ->addSelect('reviews')
            ->setParameter('user', $user)
            ->where('reviews.is_approved = true')
            ->andWhere('uploadedDoc.user_id = :user')
            ->getQuery()
            ->getResult()
            ;
    }

}
