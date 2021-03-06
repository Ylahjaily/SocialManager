<?php

namespace App\Repository;

use App\Entity\Proposal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Proposal|null find($id, $lockMode = null, $lockVersion = null)
 * @method Proposal|null findOneBy(array $criteria, array $orderBy = null)
 * @method Proposal[]    findAll()
 * @method Proposal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProposalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Proposal::class);
    }

     /**
      * @return Proposal[] Returns an array of Proposal objects
     */
    public function findApprovedProposals()
    {
        return $this->createQueryBuilder('proposal')
            ->innerJoin('proposal.reviews','reviews')
            ->addSelect('reviews')
            ->where('reviews.is_approved = true')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Proposal[] Returns an array of Proposal objects
     */
    public function findUnProcessedProposals()
    {
        return $this->createQueryBuilder('proposal')
            ->select('proposal')
            ->where( 'SIZE(proposal.reviews) = 0')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return Proposal[] Returns an array of Proposal objects
     */
    public function findApprovedProposalByReviewer($user)
    {
        return $this->createQueryBuilder('proposal')
            ->innerJoin('proposal.reviews','reviews')
            ->addSelect('reviews')
            ->setParameter('user', $user)
            ->where('reviews.is_approved = true')
            ->andWhere('reviews.user_id = :user')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return Proposal[] Returns an array of Proposal objects
     */
    public function findRejectedProposalsByReviewer($user)
    {
        return $this->createQueryBuilder('proposal')
            ->innerJoin('proposal.reviews','reviews')
            ->addSelect('reviews')
            ->setParameter('user', $user)
            ->where('reviews.is_approved = false')
            ->andWhere('reviews.user_id = :user')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return Proposal[] Returns an array of Proposal objects
     */
    public function findRejectedProposalsByMember($user)
    {
        return $this->createQueryBuilder('proposal')
            ->innerJoin('proposal.reviews','reviews')
            ->addSelect('reviews')
            ->setParameter('user', $user)
            ->where('reviews.is_approved = false')
            ->andWhere('proposal.user_id = :user')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return Proposal[] Returns an array of Proposal objects
     */
    public function findPublishedProposals()
    {
        return $this->createQueryBuilder('proposal')
            ->select('proposal')
            ->where('proposal.is_published = true')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return Proposal[] Returns an array of Proposal objects
     */
    public function findPublishedProposalsByUser($user)
    {
        return $this->createQueryBuilder('proposal')
            ->select('proposal')
            ->setParameter('user', $user)
            ->where('proposal.is_published = true')
            ->andWhere('proposal.user_id = :user')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return Proposal[] Returns an array of Proposal objects
     */
    public function findApprovedProposalsByMember($user)
    {
        return $this->createQueryBuilder('proposal')
            ->innerJoin('proposal.reviews','reviews')
            ->addSelect('reviews')
            ->setParameter('user', $user)
            ->where('reviews.is_approved = true')
            ->andWhere('proposal.user_id = :user')
            ->getQuery()
            ->getResult()
            ;
    }

}
