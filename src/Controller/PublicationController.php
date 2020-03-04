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
     */
    public function getApiPublications()
    {
        $publicationRepo=$this->publicationRepo->findAll();
        return $this->view($publicationRepo);
    }

    /**
     * @Rest\Get("/api/publications/{id}")
     * @Rest\View(serializerGroups={"publication"})
     */
    public function getApiPublication(Publication $publication)
    {
        return $this->view($publication);
    }

    /**
     * @Rest\Post("/api/publications/")
     * @Rest\View(serializerGroups={"publication"})
     */
    public function postApiPublication(Request $request, ProposalRepository $proposalRepository, UserRepository $userRepository, SocialNetworkRepository $socialRepository, EntityManagerInterface $em)
    {
        $publication=new Publication();

        if(!is_null($request->get('user_id'))) {
            $user = $userRepository->find($request->get('user_id'));
            if(!is_null($user)) {
                $publication->setUserId($user);
            }
        }

        if(!is_null($request->get('proposal_id'))) {
            $proposal = $proposalRepository->find($request->get('proposal_id'));
            if(!is_null($proposal)) {
                $publication->setProposalId($user);
            }
        }

        if(!is_null($request->get('social_network_id'))) {
            $social = $socialRepository->find($request->get('social_network_id'));
            if(!is_null($social)) {
                $publication->setSocialNetworkId($social);
            }
        }

        $em->persist($publication);
        $em->flush();

        return $this->view($publication);

    }

    /**
     * @Rest\Delete("api/publications/{id}")
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
     */
    public function getApiPublicationsByCommunciant(User $user)
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
