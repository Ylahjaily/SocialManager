<?php

namespace App\Controller;

use App\Entity\Comment;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use App\Repository\ProposalRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Proposal;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Swagger\Annotations as SWG;

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
     * @Rest\View(serializerGroups={"comment"})
     * @SWG\Response(
     *   response = 200,
     *   description = "return list of comments"
     * )
     */
    public function getApiComments()
    {
        $comments=$this->commentRepo->findAll();
        return $this->view($comments);
    }

    /**
     * @Rest\Get("/api/comments/{id}")
     * @Rest\View(serializerGroups={"comment"})
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
    public function getApiComment(Comment $comment)
    {
        return $this->view($comment);
    }

    /**
     * @Rest\Post("/api/profile/proposals/{id}/comments/")
     * @Rest\View(serializerGroups={"comment"})
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
    public function postApiComment(Request $request, Proposal $proposal, UserRepository $userRepository, EntityManagerInterface $em)
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
        $em->persist($comment);
        $em->flush();

        return $this->view($comment);

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
        if($comment)
        {
            $em->remove($comment);
            $em->flush();
            return $this->view("La suppression a bien été effectuée");
        }
    }

    /**
     * @Rest\Patch("api/comments/{id}")
     * @Rest\View(serializerGroups={"comment"})
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
    public function patchApiComment(Comment $comment, Request $request,EntityManagerInterface $em)
    {
        foreach(static::$patchCommentModifiableAttributes as $attribute => $setter) {
            if(is_null($request->get($attribute))) {
                continue;
            }
            $comment->$setter($request->get($attribute));
        }
        $em->flush();
        return $this->view($comment);
    }

    /**
     * @Rest\Get("/api/profile/proposals/{id}/comments")
     * @Rest\View(serializerGroups={"comment"})
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
    public function getApiCommentsByProposal(Proposal $proposal)
    {
        if(!$proposal) {
            throw new NotFoundHttpException('This proposal does not exist');
        }
        $comments=$this->commentRepo->findCommentsByProposal($proposal);

        if(!$comments) {
            throw new NotFoundHttpException('There is no comments...');
        }
        return $this->view($comments);
    }
}
