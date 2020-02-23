<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Review;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Repository\ReviewRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;
use App\Repository\ProposalRepository;

class ReviewController extends AbstractFOSRestController
{
    private $reviewRepo;

    public function __construct(ReviewRepository $reviewRepo)
    {
        $this->reviewRepo=$reviewRepo;
    }

    /**
     * @Rest\Get("/api/reviews/")
     */
    public function getApiReviews()
    {
        $reviews=$this->reviewRepo->findAll();
        return $this->view($reviews);
    }
    
    /**
     * @Rest\Get("/api/reviews/{id}")
     */
    public function getApiReview(Review $review)
    {
        return $this->view($review);
    }

    /**
     * @Rest\Post("/api/reviews/")
     */
    public function postApiReview(Request $request, ProposalRepository $proposalRepository, UserRepository $userRepository, EntityManagerInterface $em)
    {
        $review=new Review();
        $review->setIsApproved(false);

        if(!is_null($request->get('proposal_id'))) {
            $proposal = $proposalRepository->find($request->get('proposal_id')); 
            
            if(!is_null($proposal)) {
                $review->setProposalId($proposal);
            }
        }

        if(!is_null($request->get('user_id'))) {
            $user = $userRepository->find($request->get('user_id'));
            if(!is_null($user)) {
                $review->setUserId($user);
            }
        }
        
        $em->persist($review);
        $em->flush();

        return $this->view($review);
    
    }

}
