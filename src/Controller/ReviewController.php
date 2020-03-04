<?php

namespace App\Controller;

use App\Entity\Review;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;
use App\Repository\ProposalRepository;

class ReviewController extends AbstractFOSRestController
{
    private $reviewRepo;

    static private $patchReviewModifiableAttributes = [
        'is_approved' => 'setIsApproved',
        'decision_at' => 'setDecisionAt',
    ];

    public function __construct(ReviewRepository $reviewRepo)
    {
        $this->reviewRepo=$reviewRepo;
    }

    /**
     * @Rest\Get("/api/reviews/")
     * @Rest\View(serializerGroups={"review"})
     */
    public function getApiReviews()
    {
        $reviews=$this->reviewRepo->findAll();
        return $this->view($reviews);
    }

    /**
     * @Rest\Get("/api/reviews/{id}")
     * @Rest\View(serializerGroups={"review"})
     */
    public function getApiReview(Review $review)
    {
        return $this->view($review);
    }

    /**
     * @Rest\Post("/api/reviews/")
     * @Rest\View(serializerGroups={"review"})
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

    /**
     * @Rest\Delete("api/reviews/{id}")
     */
    public function deleteApiReview(Review $review, EntityManagerInterface $em)
    {
        if($review)
        {
            $em->remove($review);
            $em->flush();
            return $this->view("La suppression a bien été effectuée");
        }
    }

    /**
     * @Rest\Patch("/api/reviewer/reviews/{id}")
     * @Rest\View(serializerGroups={"review"})
     */
    public function patchApiReview(Review $review, Request $request,EntityManagerInterface $em)
    {
        foreach(static::$patchReviewModifiableAttributes as $attribute => $setter) {
            if(is_null($request->get($attribute))) {
                continue;
            }
            $review->$setter($request->get($attribute));
        }
        $em->flush();
        return $this->view($review);
    }

}
