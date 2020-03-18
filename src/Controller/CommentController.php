<?php

namespace App\Controller;

use App\Entity\Comment;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use App\Repository\ProposalRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Proposal;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Swagger\Annotations as SWG;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CommentController extends AbstractFOSRestController
{
    private $commentRepo;

    static private $postCommentRequiredAttributes = [
        'content' => 'setContent',
    ];

    static private $patchCommentModifiableAttributes = [
        'content' => 'setContent',
    ];

    public function __construct(CommentRepository $commentRepo)
    {
        $this->commentRepo=$commentRepo;
    }

    /**
     * @Rest\Get("/api/comments/")
     * @SWG\Response(
     *   response = 200,
     *   description = "return list of comments"
     * )
     */
    public function getApiComments(SerializerInterface $serializer)
    {
        $comments=$this->commentRepo->findAll();

        if(!$comments) {
            throw new NotFoundHttpException('The is no comments yet');
        }

        $json = $serializer->serialize(
            $comments,
            'json', ['groups' => 'comment']
        );

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent($json);
        $response->setStatusCode(200);
        return $response;
    }

    /**
     * @Rest\Get("/api/comments/{id}")
     * @SWG\Parameter(
     *  name = "id",
     *  in = "path",
     *  type = "number",
     *  description="The ID of the comment",
     *  required=true
     * )
     * @SWG\Response(
     *  response = 200,
     *  description = "return one comment"
     * )
     * @SWG\Response(
     *  response = 404,
     *  description = "comment not found"
     * )
     */
    public function getApiComment(Comment $comment, SerializerInterface $serializer)
    {

        if(!$comment) {
            throw new NotFoundHttpException('This comment does not exist');
        }

        $json = $serializer->serialize(
            $comment,
            'json', ['groups' => 'comment']
        );

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent($json);
        $response->setStatusCode(200);
        return $response;
    }

    /**
     * @Rest\Post("/api/profile/proposals/{id}/comments/")
     * @SWG\Parameter(
     *  name = "id",
     *  in = "path",
     *  type = "number",
     *  description = "the id of the Proposal",
     *  required = true
     * )
     * @SWG\Parameter(
     *  name = "content",
     *  in = "body",
     *  type = "text",
     *  description = "the content of the new Comment",
     *  required = true,
     *  @SWG\Schema(
     *      example = "content -xxx",
     *      type = "text"
     *  )
     * )
     * @SWG\Parameter(
     *  name = "user_id",
     *  in = "body",
     *  type = "number",
     *  description = "the ID of the User who adds a comment",
     *  required = true,
     *  @SWG\Schema(
     *      example = "2",
     *      type = "number"
     *  )
     * )
     * @SWG\Response(
     *  response = 201,
     *  description = "Comment created"
     * )
     * @SWG\Response(
     *  response = 400,
     *  description = "Uncorect request"
     * )
     */
    public function postApiComment(Request $request, Proposal $proposal, UserRepository $userRepository,ValidatorInterface $validator, EntityManagerInterface $em, SerializerInterface $serializer)
    {
        $comment=new Comment();

        if(!$proposal) {
            throw new NotFoundHttpException('This proposal does not exist');
        }
        $comment->setProposalId($proposal);

        foreach(static::$postCommentRequiredAttributes as $attribute => $setter) {
            if(is_null($request->get($attribute))) {
                continue;
            }
            $comment->$setter($request->get($attribute));
        }

        if(!is_null($request->get('user_id'))) {
            $user = $userRepository->find($request->get('user_id'));
            if(!is_null($user)) {
                $comment->setUserId($user);
            }
        }

        $validationErrors = $validator->validate($proposal);

        /** @var ConstraintViolation $constraintViolation */
        foreach($validationErrors as $constraintViolation) {
            $message = $constraintViolation->getMessage();
            $propertyPath = $constraintViolation->getPropertyPath();
            $errors[] = ['property' => $propertyPath, 'message' => $message];
        }

        if(!empty($errors)) {
            return new JsonResponse($errors, 400);
        }

        $em->persist($comment);
        $em->flush();

        $json = $serializer->serialize(
            $comment,
            'json', ['groups' => 'comment']
        );

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent($json);
        $response->setStatusCode(201);
        return $response;
    }

    /**
     * @Rest\Delete("api/comments/{id}")
     * @SWG\Parameter(
     *  name = "id",
     *  in = "path",
     *  type = "number",
     *  description = "the id of the comment we want to delete",
     *  required = true
     * )
     * @SWG\Response(
     *  response = 204,
     *  description = "Comment deleted"
     * )
     * @SWG\Response(
     *  response = 404,
     *  description = "Comment not found"
     * )
     * @SWG\Response(
     *  response = 403,
     *  description = "User not allowed"
     * )
     */
    public function deleteApiComment(Comment $comment, EntityManagerInterface $em)
    {
        if(!$comment)
        {
            throw new NotFoundHttpException('This comment does not exist');
        }
        $em->remove($comment);
        $em->flush();

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent("Comment deleted");
        $response->setStatusCode(204);
        return $response;
    }

    /**
     * @Rest\Patch("api/comments/{id}")
     * @SWG\Parameter(
     *  name = "id",
     *  in = "path",
     *  type = "number",
     *  description = "the Id of the Comment",
     *  required = true
     * )
     * @SWG\Parameter(
     *  name = "content",
     *  in = "body",
     *  type = "text",
     *  description = "The content of the comment",
     *  required = true,
     *  @SWG\Schema(
     *      example = "content -xxx",
     *      type="text"
     *  )
     * )
     * @SWG\Response(
     *  response = 200,
     *  description = "comment updated"
     * )
     * @SWG\Response(
     *  response = 403,
     *  description = "User not allowed"
     * )
     * @SWG\Response(
     *  response = 404,
     *  description = "Comment doesn't exist"
     * )
     */
    public function patchApiComment(Comment $comment, Request $request,EntityManagerInterface $em, ValidatorInterface $validator, SerializerInterface $serializer)
    {
        if(!$comment) {
            throw new NotFoundHttpException('This comment does not exist');
        }

        foreach(static::$patchCommentModifiableAttributes as $attribute => $setter) {
            if(is_null($request->get($attribute))) {
                continue;
            }
            $comment->$setter($request->get($attribute));
        }

        $validationErrors = $validator->validate($comment);

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
            $comment,
            'json', ['groups' => 'comment']
        );

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent($json);
        $response->setStatusCode(200);
        return $response;
    }

    /**
     * @Rest\Get("/api/profile/proposals/{id}/comments")
     * @SWG\Parameter(
     *  name = "id",
     *  in = "path",
     *  type = "number",
     *  description = "the ID of the proposal",
     *  required = true
     * )
     * @SWG\Response(
     *  response = 200,
     *  description = "list of the comments by proposal"
     * )
     * @SWG\Response(
     *  response = 404,
     *  description = "Proposal doesn't exist"
     * )
     */
    public function getApiCommentsByProposal(Proposal $proposal, SerializerInterface $serializer)
    {
        if(!$proposal) {
            throw new NotFoundHttpException('This proposal does not exist');
        }
        $comments=$this->commentRepo->findCommentsByProposal($proposal);

        if(!$comments) {
            throw new NotFoundHttpException('There is no comments...');
        }

        $json = $serializer->serialize(
            $comments,
            'json', ['groups' => 'comment']
        );

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent($json);
        $response->setStatusCode(200);
        return $response;
    }
}
