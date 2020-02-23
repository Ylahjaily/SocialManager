<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Like;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Repository\LikeRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;
use App\Repository\ProposalRepository;

class LikeController extends AbstractFOSRestController
{
    private $likeRepo;

    public function __construct(LikeRepository $likeRepo)
    {
        $this->likeRepo=$likeRepo;
    }

    /**
     * @Rest\Get("/api/likes/")
     */
    public function getApiLikes()
    {
        $likes=$this->likeRepo->findAll();
        return $this->view($likes);
    }
    
    /**
     * @Rest\Get("/api/likes/{id}")
     */
    public function getApiLike(Like $like)
    {
        return $this->view($like);
    }

    /**
     * @Rest\Post("/api/likes/")
     */
    public function postApiLike(Request $request, ProposalRepository $proposalRepository, UserRepository $userRepository, EntityManagerInterface $em)
    {
        $like=new Like();

        if(!is_null($request->get('proposal_id'))) {
            $proposal = $proposalRepository->find($request->get('proposal_id'));     
            if(!is_null($proposal)) {
                $like->setProposalId($proposal);
            }
        }

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

}
