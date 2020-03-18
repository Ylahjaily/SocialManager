<?php

namespace App\Controller;

use App\Entity\Review;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;
use App\Repository\ProposalRepository;
use Swagger\Annotations as SWG;
use App\Entity\Proposal;
use App\Entity\UploadedDocument;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class ReviewController extends AbstractFOSRestController
{
    private $reviewRepo;

    static private $patchReviewModifiableAttributes = [
        'is_approved' => 'setIsApproved'
    ];

    public function __construct(ReviewRepository $reviewRepo)
    {
        $this->reviewRepo=$reviewRepo;
    }

    /**
     * @Rest\Get("/api/reviews/")
     * @SWG\Response(
     *   response = 200,
     *   description = "return list of reviews"
     * )
     */
    public function getApiReviews(SerializerInterface $serializer)
    {
        $reviews=$this->reviewRepo->findAll();

        if(!$reviews) {
            throw new NotFoundHttpException('Reviews do not exist');
        }

        $json = $serializer->serialize(
            $reviews,
            'json', ['groups' => 'review']
        );

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent($json);
        $response->setStatusCode(200);
        return $response;
    }

    /**
     * @Rest\Get("/api/reviews/{id}")
     * @SWG\Parameter(
     *  name = "id",
     *  in = "path",
     *  type = "number",
     *  description="The ID of the review",
     *  required=true
     * )
     * @SWG\Response(
     *  response = 200,
     *  description = "return one review"
     * )
     * @SWG\Response(
     *  response = 404,
     *  description = "review not found"
     * )
     */
    public function getApiReview(Review $review,SerializerInterface $serializer)
    {
        if(!$review) {
            throw new NotFoundHttpException('Review does not exist');
        }

        $json = $serializer->serialize(
            $review,
            'json', ['groups' => 'review']
        );

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent($json);
        $response->setStatusCode(200);
        return $response;
    }

    /**
     * @Rest\Post("/api/proposals/{id}/reviews/")
     * @SWG\Parameter(
     *  name = "id",
     *  in = "path",
     *  type = "number",
     *  description = "the id of the proposal",
     *  required = true
     * )
     * @SWG\Parameter(
     *  name = "user_id",
     *  in = "body",
     *  type = "number",
     *  description = "the ID of the User who adds a review",
     *  required = true,
     *  @SWG\Schema(
     *      example = "2",
     *      type = "number"
     *  )
     * )
     * @SWG\Response(
     *  response = 201,
     *  description = "Review created"
     * )
     * @SWG\Response(
     *  response = 400,
     *  description = "Uncorect request"
     * )
     */

    public function postApiProposalReview(Request $request, Proposal $proposal, UserRepository $userRepository, EntityManagerInterface $em)

  
    {
        $review=new Review();
        $review->setIsApproved(false);

        if(!$review) {
            throw new NotFoundHttpException('This review does not exist');
        }
        $review->setProposalId($proposal);

        if(!is_null($request->get('user_id'))) {
            $user = $userRepository->find($request->get('user_id'));
            if(!is_null($user)) {
                $review->setUserId($user);
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

        $em->persist($review);
        $em->flush();

        $json = $serializer->serialize(
            $proposal,
            'json', ['groups' => 'review']
        );

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent($json);
        $response->setStatusCode(201);
        return $response;
    }

    /**
     * @Rest\Post("/api/up_docs/{id}/reviews/")
     * @Rest\View(serializerGroups={"review"})
     * @SWG\Parameter(
     *  name = "id",
     *  in = "path",
     *  type = "number",
     *  description = "the id of the uploaded document",
     *  required = true
     * )
     * @SWG\Parameter(
     *  name = "user_id",
     *  in = "body",
     *  type = "number",
     *  description = "the ID of the User who adds a review",
     *  required = true,
     *  @SWG\Schema(
     *      example = "2",
     *      type = "number"
     *  )
     * )
     * @SWG\Response(
     *  response = 201,
     *  description = "Review created"
     * )
     * @SWG\Response(
     *  response = 400,
     *  description = "Uncorect request"
     * )
     */
    public function postApiFileReview(Request $request, UploadedDocument $uploadedDoc, UserRepository $userRepository, EntityManagerInterface $em)
    {
        $review=new Review();
        $review->setIsApproved(false);

        if(!$uploadedDoc) {
            throw new NotFoundHttpException('This uploaded document does not exist');
        }
        $review->setUploadedDocumentId($uploadedDoc);

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
     * @SWG\Parameter(
     *  name = "id",
     *  in = "path",
     *  type = "number",
     *  description = "the id of the Review we want to delete",
     *  required = true
     * )
     * @SWG\Response(
     *  response = 204,
     *  description = "Review deleted"
     * )
     * @SWG\Response(
     *  response = 404,
     *  description = "Review not found"
     * )
     */
    public function deleteApiReview(Review $review, EntityManagerInterface $em)
    {
        if(!$review)
        {
            throw new NotFoundHttpException('Review does not exist');
        }
        $em->remove($review);
        $em->flush();

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent("Proposal deleted");
        $response->setStatusCode(204);
        return $response;
    }

    /**
     * @Rest\Patch("/api/reviewer/reviews/{id}")
     * @SWG\Parameter(
     *  name = "id",
     *  in = "path",
     *  type = "number",
     *  description = "the Id of the Review",
     *  required = true
     * )
     * @SWG\Parameter(
     *  name = "is_approved",
     *  in = "body",
     *  type = "boolean",
     *  description = "The approbation(or not) of the reviewed proposal",
     *  required = true,
     *  @SWG\Schema(
     *      example = "1",
     *      type="boolean"
     *  )
     * )
     * @SWG\Response(
     *  response = 200,
     *  description = "Review updated"
     * )
     * @SWG\Response(
     *  response = 403,
     *  description = "User not allowed"
     * )
     * @SWG\Response(
     *  response = 404,
     *  description = "Review doesn't exist"
     * )
     */
    public function patchApiReview(Review $review, Request $request,ValidatorInterface $validator,SerializerInterface $serializer, EntityManagerInterface $em)
    {
        if(!$review) {
            throw new NotFoundHttpException('This review does not exist');
        }

        foreach(static::$patchReviewModifiableAttributes as $attribute => $setter) {
            if(is_null($request->get($attribute))) {
                continue;
            }
            $review->$setter($request->get($attribute));
            $review->setDecisionAt(new \DateTime('now'));
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

        $em->flush();

        $json = $serializer->serialize(
            $review,
            'json', ['groups' => 'review']
        );

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent($json);
        $response->setStatusCode(200);
        return $response;
    }

}
