<?php

namespace App\Controller;

use App\Entity\ReviewComment;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Repository\ReviewCommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;
use App\Repository\ReviewRepository;
use App\Entity\Review;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ReviewCommentController extends AbstractFOSRestController
{
    private $reviewCommentRepo;

    static private $postReviewCommentRequiredAttributes = [
        'comments' => 'setComments',
    ];

    static private $patchReviewCommentModifiableAttributes = [
        'comments' => 'setComments',
    ];

    public function __construct(ReviewCommentRepository $reviewCommentRepo)
    {
        $this->reviewCommentRepo=$reviewCommentRepo;
    }

    /**
     * @Rest\Get("/api/review_comments/")
     * @Rest\View(serializerGroups={"reviewComment"})
     */
    public function getApiReviewComments()
    {
        $review_comments=$this->reviewCommentRepo->findAll();
        return $this->view($review_comments);
    }

    /**
     * @Rest\Get("/api/review_comments/{id}")
     * @Rest\View(serializerGroups={"reviewComment"})
     */
    public function getApiReviewComment(ReviewComment $reviewComment)
    {
        return $this->view($reviewComment);
    }

    /**
     * @Rest\Post("/api/profile/review/{review_id}/review_comments/")
     * @Rest\View(serializerGroups={"reviewComment"})
     */
    public function postApiReviewComment(Request $request, ReviewRepository $reviewRepository, UserRepository $userRepository, EntityManagerInterface $em)
    {
        $reviewComment=new ReviewComment();

        foreach(static::$postReviewCommentRequiredAttributes as $attribute => $setter) {
            if(is_null($request->get($attribute))) {
                continue;
            }
            $reviewComment->$setter($request->get($attribute));
        }

        if(!is_null($request->get('review_id'))) {
            $review = $reviewRepository->find($request->get('review_id'));
            if(!is_null($review)) {
                $reviewComment->setReviewId($review);
            }
        }

        if(!is_null($request->get('user_id'))) {
            $user = $userRepository->find($request->get('user_id'));
            if(!is_null($user)) {
                $reviewComment->setUserId($user);
            }
        }

        $em->persist($reviewComment);
        $em->flush();

        return $this->view($reviewComment);

    }

    /**
     * @Rest\Delete("api/review_comments/{id}")
     */
    public function deleteApiReviewComment(ReviewComment $reviewComment, EntityManagerInterface $em)
    {
        if($reviewComment)
        {
            $em->remove($reviewComment);
            $em->flush();
            return $this->view("La suppression a bien été effectuée");
        }
    }

    /**
     * @Rest\Patch("api/review_comments/{id}")
     * @Rest\View(serializerGroups={"reviewComment"})
     */
    public function patchApiReviewComment(ReviewComment $reviewComment, Request $request,EntityManagerInterface $em)
    {
        foreach(static::$patchReviewCommentModifiableAttributes as $attribute => $setter) {
            if(is_null($request->get($attribute))) {
                continue;
            }
            $reviewComment->$setter($request->get($attribute));
        }
        $em->flush();
        return $this->view($reviewComment);
    }

    /**
     * @Rest\Get("/api/review/{id}/review_comments")
     * @Rest\View(serializerGroups={"reviewComment"})
     */
    public function getApiReviewCommentsByReview(Review $review)
    {
        if(!$review) {
            throw new NotFoundHttpException('This review does not exist');
        }
        $review_comments=$this->reviewCommentRepo->findReviewCommentsByReview($review);

        if(!$review_comments) {
            throw new NotFoundHttpException('Review comments do not exist');
        }
        return $this->view($review_comments);
    }

}
