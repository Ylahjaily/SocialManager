<?php

namespace App\Controller;

use App\Entity\Like;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Repository\LikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;
use App\Repository\ProposalRepository;
use App\Entity\Proposal;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Swagger\Annotations as SWG;

class LikeController extends AbstractFOSRestController
{
    private $likeRepo;

    public function __construct(LikeRepository $likeRepo)
    {
        $this->likeRepo=$likeRepo;
    }

    /**
     * @Rest\Get("/api/likes/")
     * @Rest\View(serializerGroups={"like"})
     * @SWG\Response(
     *   response = 200,
     *   description = "return list of likes"
     * )
     */
    public function getApiLikes()
    {
        $likes=$this->likeRepo->findAll();
        return $this->view($likes);
    }

    /**
     * @Rest\Get("/api/likes/{id}")
     * @Rest\View(serializerGroups={"like"})
     * @SWG\Parameter(
     *  name = "id",
     *  in = "path",
     *  type = "number",
     *  description="The ID of the like",
     *  required=true
     * )
     * @SWG\Response(
     *  response = 200,
     *  description = "return one like"
     * )
     * @SWG\Response(
     *  response = 404,
     *  description = "like not found"
     * )
     */
    public function getApiLike(Like $like)
    {
        return $this->view($like);
    }

    /**
     * @Rest\Post("/api/profile/proposals/{id}/likes/")
     * @Rest\View(serializerGroups={"like"})
     * @SWG\Parameter(
     *  name = "id",
     *  in = "path",
     *  type = "number",
     *  description = "the id of the Proposal",
     *  required = true
     * )
     * @SWG\Parameter(
     *  name = "user_id",
     *  in = "body",
     *  type = "number",
     *  description = "the id of the User who likes",
     *  required = true,
     *  @SWG\Schema(
     *      example = "3",
     *      type = "number"
     *  )
     * )
     * @SWG\Response(
     *  response = 201,
     *  description = "like created"
     * )
     * @SWG\Response(
     *  response = 400,
     *  description = "bad request"
     * )
     */
    public function postApiLike(Request $request, Proposal $proposal, UserRepository $userRepository, EntityManagerInterface $em)
    {
        $like=new Like();

        if(!$proposal) {
            throw new NotFoundHttpException('This proposal does not exist');
        }
        $like->setProposalId($proposal);

        if(!is_null($request->get('user_id'))) {
            $user = $userRepository->find($request->get('user_id'));
            if(!is_null($user)) {
                $like->setUserId($user);
            }
        }

        $em->persist($like);
        $em->flush();

        return $this->view($like);

    }

    /**
     * @Rest\Delete("api/likes/{id}")
     * @SWG\Parameter(
     *  name = "id",
     *  in = "path",
     *  type = "number",
     *  description = "the id of the like we want to delete",
     *  required = true
     * )
     * @SWG\Response(
     *  response = 204,
     *  description = "Like deleted"
     * )
     * @SWG\Response(
     *  response = 404,
     *  description = "Like not found"
     * )
     * @SWG\Response(
     *  response = 403,
     *  description = "User not allowed"
     * )
     */
    public function deleteApiLike(Like $like, EntityManagerInterface $em)
    {
        if($like)
        {
            $em->remove($like);
            $em->flush();
            return $this->view("La suppression a bien été effectuée");
        }

    }

    /**
     * @Rest\Get("/api/profile/proposals/{id}/likes")
     * @Rest\View(serializerGroups={"like"})
     * @SWG\Parameter(
     *  name = "id",
     *  in = "path",
     *  type = "number",
     *  description = "the ID of the proposal",
     *  required = true
     * )
     * @SWG\Response(
     *  response = 200,
     *  description = "list of the likes by proposal"
     * )
     * @SWG\Response(
     *  response = 404,
     *  description = "Proposal doesn't exist"
     * )
     */
    public function getApiLikesByProposal(Proposal $proposal)
    {
        if(!$proposal) {
            throw new NotFoundHttpException('This proposal does not exist');
        }
        $likes=$this->likeRepo->findLikesByProposal($proposal);

        if(!$likes) {
            throw new NotFoundHttpException('There is no likes...');
        }
        return $this->view($likes);
    }

}
