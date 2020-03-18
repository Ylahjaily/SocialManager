<?php

namespace App\Controller;

use App\Entity\ReviewComment;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Repository\ReviewCommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;
use App\Repository\ReviewRepository;
use App\Entity\Review;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Swagger\Annotations as SWG;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
     * @SWG\Response(
     *   response = 200,
     *   description = "return list of review comments"
     * )
     */
    public function getApiReviewComments(SerializerInterface $serializer)
    {
        $review_comments=$this->reviewCommentRepo->findAll();

        if(!$review_comments) {
            throw new NotFoundHttpException('There is no review comments');
        }

        $json = $serializer->serialize(
            $review_comments,
            'json', ['groups' => 'reviewComment']
        );

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent($json);
        $response->setStatusCode(200);
        return $response;
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
    public function getApiReviewComment(ReviewComment $reviewComment, SerializerInterface $serializer)
    {

        if(!$reviewComment) {
            throw new NotFoundHttpException('This comment does not exist');
        }

        $json = $serializer->serialize(
            $reviewComment,
            'json', ['groups' => 'reviewComment']
        );

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent($json);
        $response->setStatusCode(200);
        return $response;
    }

    /**
     * @Rest\Post("/api/profile/reviews/{id}/review_comments/")
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
    public function postApiReviewComment(Request $request, Review $review, UserRepository $userRepository,ValidatorInterface $validator,SerializerInterface $serializer, EntityManagerInterface $em)
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

        $validationErrors = $validator->validate($review);

        /** @var ConstraintViolation $constraintViolation */
        foreach($validationErrors as $constraintViolation) {
            $message = $constraintViolation->getMessage();
            $propertyPath = $constraintViolation->getPropertyPath();
            $errors[] = ['property' => $propertyPath, 'message' => $message];
        }

        if(!empty($errors)) {
            return new JsonResponse($errors, 400);
        }

        $em->persist($reviewComment);
        $em->flush();

        $json = $serializer->serialize(
            $reviewComment,
            'json', ['groups' => 'reviewComment']
        );

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent($json);
        $response->setStatusCode(201);
        return $response;
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
        if(!$reviewComment)
        {
            throw new NotFoundHttpException('Review does not exist');
        }
        $em->remove($reviewComment);
        $em->flush();

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent("Comment deleted");
        $response->setStatusCode(204);
        return $response;
    }

    /**
     * @Rest\Patch("api/review_comments/{id}")
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
    public function patchApiReviewComment(ReviewComment $reviewComment, Request $request,EntityManagerInterface $em, ValidatorInterface $validator, SerializerInterface $serializer)
    {
        if(!$reviewComment) {
            throw new NotFoundHttpException('This comment does not exist for this review');
        }

        foreach(static::$patchReviewCommentModifiableAttributes as $attribute => $setter) {
            if(is_null($request->get($attribute))) {
                continue;
            }
            $reviewComment->$setter($request->get($attribute));
        }

        $validationErrors = $validator->validate($reviewComment);

        /** @var ConstraintViolation $constraintViolation */
        foreach($validationErrors as $constraintViolation) {
            $message = $constraintViolation->getMessage();
            $propertyPath = $constraintViolation->getPropertyPath();
            $errors[] = ['property' => $propertyPath, 'message' => $message];
        }

        if(!empty($errors)) {
            return new JsonResponse($errors, 400);
        }

        $em->flush();

        $json = $serializer->serialize(
            $reviewComment,
            'json', ['groups' => 'reviewComment']
        );

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent($json);
        $response->setStatusCode(200);
        return $response;
    }

    /**
     * @Rest\Get("/api/review/{id}/review_comments")
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
    public function getApiReviewCommentsByReview(Review $review, SerializerInterface $serializer)
    {
        if(!$review) {
            throw new NotFoundHttpException('This review does not exist');
        }

        $review_comments=$this->reviewCommentRepo->findReviewCommentsByReview($review);

        if(!$review_comments) {
            throw new NotFoundHttpException('Review comments do not exist');
        }

        $json = $serializer->serialize(
            $review_comments,
            'json', ['groups' => 'reviewComment']
        );

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent($json);
        $response->setStatusCode(200);
        return $response;
    }
}
