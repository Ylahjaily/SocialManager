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
use Swagger\Annotations as SWG;

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
     * @SWG\Response(
     *   response = 200,
     *   description = "return list of review comments"
     * )
     */
    public function getApiReviewComments()
    {
        $review_comments=$this->reviewCommentRepo->findAll();
        return $this->view($review_comments);
    }

    /**
     * @Rest\Get("/api/review_comments/{id}")
     * @Rest\View(serializerGroups={"reviewComment"})
     * @SWG\Parameter(
     *  name = "id",
     *  in = "path",
     *  type = "number",
     *  description="The ID of the review comment",
     *  required=true
     * )
     * @SWG\Response(
     *  response = 200,
     *  description = "return one review comment"
     * )
     * @SWG\Response(
     *  response = 404,
     *  description = "review comment not found"
     * )
     */
    public function getApiReviewComment(ReviewComment $reviewComment)
    {
        return $this->view($reviewComment);
    }

    /**
     * @Rest\Post("/api/profile/reviews/{id}/review_comments/")
     * @Rest\View(serializerGroups={"reviewComment"})
     * @SWG\Parameter(
     *  name = "id",
     *  in = "path",
     *  type = "number",
     *  description = "the id of the Review",
     *  required = true
     * )
     * @SWG\Parameter(
     *  name = "comments",
     *  in = "body",
     *  type = "text",
     *  description = "the comments of the new reviewComment",
     *  required = true,
     *  @SWG\Schema(
     *      example = "reviewComments -xxx",
     *      type = "text"
     *  )
     * )
     * @SWG\Parameter(
     *  name = "user_id",
     *  in = "body",
     *  type = "number",
     *  description = "the ID of the User who adds a review comment",
     *  required = true,
     *  @SWG\Schema(
     *      example = "2",
     *      type = "number"
     *  )
     * )
     * @SWG\Response(
     *  response = 201,
     *  description = "Review Comment created"
     * )
     * @SWG\Response(
     *  response = 400,
     *  description = "Uncorect request"
     * )
     */
    public function postApiReviewComment(Request $request, Review $review, UserRepository $userRepository, EntityManagerInterface $em)
    {
        $reviewComment=new ReviewComment();

        if(!$review) {
            throw new NotFoundHttpException('This review does not exist');
        }
        $reviewComment->setReviewId($review);

        foreach(static::$postReviewCommentRequiredAttributes as $attribute => $setter) {
            if(is_null($request->get($attribute))) {
                continue;
            }
            $reviewComment->$setter($request->get($attribute));
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
     * @SWG\Parameter(
     *  name = "id",
     *  in = "path",
     *  type = "number",
     *  description = "the id of the ReviewComment we want to delete",
     *  required = true
     * )
     * @SWG\Response(
     *  response = 204,
     *  description = "Review Comment deleted"
     * )
     * @SWG\Response(
     *  response = 404,
     *  description = "Review Comment not found"
     * )
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
     * @SWG\Parameter(
     *  name = "id",
     *  in = "path",
     *  type = "number",
     *  description = "the Id of the Review Comment",
     *  required = true
     * )
     * @SWG\Parameter(
     *  name = "comments",
     *  in = "body",
     *  type = "text",
     *  description = "The comments of the Review Comment",
     *  required = true,
     *  @SWG\Schema(
     *      example = "content -xxx",
     *      type="text"
     *  )
     * )
     * @SWG\Response(
     *  response = 200,
     *  description = "review Comment updated"
     * )
     * @SWG\Response(
     *  response = 403,
     *  description = "User not allowed"
     * )
     * @SWG\Response(
     *  response = 404,
     *  description = "Review Comment doesn't exist"
     * )
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
     * @SWG\Parameter(
     *  name = "id",
     *  in = "path",
     *  type = "number",
     *  description = "the ID of the review",
     *  required = true
     * )
     * @SWG\Response(
     *  response = 200,
     *  description = "list of the review comments by review"
     * )
     * @SWG\Response(
     *  response = 404,
     *  description = "Review doesn't exist"
     * )
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
