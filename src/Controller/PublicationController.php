<?php

namespace App\Controller;

use App\Entity\Publication;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Repository\PublicationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;
use App\Repository\ProposalRepository;
use App\Repository\SocialNetworkRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Entity\User;
use Swagger\Annotations as SWG;
use App\Entity\SocialNetwork;

class PublicationController extends AbstractFOSRestController
{
    private $publicationRepo;

    public function __construct(PublicationRepository $publicationRepo)
    {
        $this->publicationRepo=$publicationRepo;
    }

    /**
     * @Rest\Get("/api/publications/")
     * @Rest\View(serializerGroups={"publication"})
     * @SWG\Response(
     *   response = 200,
     *   description = "return list of Publications"
     * )
     */
    public function getApiPublications()
    {
        $publicationRepo=$this->publicationRepo->findAll();
        return $this->view($publicationRepo);
    }

    /**
     * @Rest\Get("/api/publications/{id}")
     * @Rest\View(serializerGroups={"publication"})
     * @SWG\Parameter(
     *  name = "id",
     *  in = "path",
     *  type = "number",
     *  description="The ID of the publication",
     *  required=true
     * )
     * @SWG\Response(
     *  response = 200,
     *  description = "return one publication"
     * )
     * @SWG\Response(
     *  response = 404,
     *  description = "Publication not found"
     * )
     */
    public function getApiPublication(Publication $publication)
    {
        return $this->view($publication);
    }

    /**
     * @Rest\Post("/api/social_network/{id}/publications/")
     * @Rest\View(serializerGroups={"publication"})
     * @SWG\Parameter(
     *  name = "id",
     *  in = "path",
     *  type = "number",
     *  description = "the id of the Social Network",
     *  required = true
     * )
     * @SWG\Parameter(
     *  name = "user_id",
     *  in = "body",
     *  type = "number",
     *  description = "the ID of the User who publishes the proposal",
     *  required = true,
     *  @SWG\Schema(
     *      example = "5",
     *      type = "number"
     *  )
     * )
     * @SWG\Parameter(
     *  name = "proposal_id",
     *  in = "body",
     *  type = "number",
     *  description = "the ID of the proposal which will be published",
     *  required = true,
     *  @SWG\Schema(
     *      example = "2",
     *      type = "number"
     *  )
     * )
     * @SWG\Response(
     *  response = 201,
     *  description = "Publication created"
     * )
     * @SWG\Response(
     *  response = 400,
     *  description = "Uncorect request"
     * )
     */
    public function postApiPublication(Request $request, ProposalRepository $proposalRepository, UserRepository $userRepository, SocialNetwork $social, EntityManagerInterface $em)
    {
        $publication=new Publication();

        if(!$social) {
            throw new NotFoundHttpException('This social network does not exist');
        }
        $publication->setSocialNetworkId($social);

        if(!is_null($request->get('user_id'))) {
            $user = $userRepository->find($request->get('user_id'));
            if(!is_null($user)) {
                $publication->setUserId($user);
            }
        }

        if(!is_null($request->get('proposal_id'))) {
            $proposal = $proposalRepository->find($request->get('proposal_id'));
            if(!is_null($proposal)) {
                $publication->setProposalId($proposal);
            }
        }

        $em->persist($publication);
        $em->flush();

        return $this->view($publication);

    }

    /**
     * @Rest\Delete("api/publications/{id}")
     * @SWG\Parameter(
     *  name = "id",
     *  in = "path",
     *  type = "number",
     *  description = "the id of the Publication we want to delete",
     *  required = true
     * )
     * @SWG\Response(
     *  response = 204,
     *  description = "Publication deleted"
     * )
     * @SWG\Response(
     *  response = 404,
     *  description = "Publication not found"
     * )
     * @SWG\Response(
     *  response = 403,
     *  description = "User not allowed"
     * )
     */
    public function deleteApiPublication(Publication $publication, EntityManagerInterface $em)
    {
        if($publication)
        {
            $em->remove($publication);
            $em->flush();
            return $this->view("La suppression a bien été effectuée");
        }

    }

    /**
     * @Rest\Get("/api/communicant/{id}/publications")
     * @Rest\View(serializerGroups={"publication"})
     * @SWG\Parameter(
     *  name = "id",
     *  in = "path",
     *  type = "number",
     *  description = "the ID of the user",
     *  required = true
     * )
     * @SWG\Response(
     *  response = 200,
     *  description = "list of the published proposals by Communicant"
     * )
     * @SWG\Response(
     *  response = 404,
     *  description = "Communicant doesn't exist"
     * )
     * @SWG\Response(
     *  response = 403,
     *  description = "User not allowed"
     * )
     */
    public function getApiPublicationsByCommunicant(User $user)
    {
        if(!$user) {
            throw new NotFoundHttpException('This communicant does not exist');
        }
        $publications=$this->publicationRepo->findPublicationsByCommunicant($user);

        if(!$publications) {
            throw new NotFoundHttpException('There is no publication by you...');
        }
        return $this->view($publications);
    }


}
