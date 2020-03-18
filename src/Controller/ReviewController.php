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
     * @Rest\View(serializerGroups={"review"})
     * @SWG\Response(
     *   response = 200,
     *   description = "return list of reviews"
     * )
     */
    public function getApiReviews()
    {
        $reviews=$this->reviewRepo->findAll();
        return $this->view($reviews);
    }

    /**
     * @Rest\Get("/api/reviews/{id}")
     * @Rest\View(serializerGroups={"review"})
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
    public function getApiReview(Review $review)
    {
        return $this->view($review);
    }

    /**
     * @Rest\Post("/api/proposals/{id}/reviews/")
     * @Rest\View(serializerGroups={"review"})
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
    public function postApiReview(Request $request, Proposal $proposal, UserRepository $userRepository, EntityManagerInterface $em)
    {
        $review=new Review();
        $review->setIsApproved(false);

        if(!$proposal) {
            throw new NotFoundHttpException('This proposal does not exist');
        }
        $review->setProposalId($proposal);

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
    public function patchApiReview(Review $review, Request $request,EntityManagerInterface $em)
    {
        foreach(static::$patchReviewModifiableAttributes as $attribute => $setter) {
            if(is_null($request->get($attribute))) {
                continue;
            }
            $review->$setter($request->get($attribute));
            $review->setDecisionAt(new \DateTime('now'));
        }
        $em->flush();
        return $this->view($review);
    }

}
