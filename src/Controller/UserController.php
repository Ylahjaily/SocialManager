<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Doctrine\ORM\EntityManagerInterface;

class UserController extends AbstractFOSRestController
{
    private $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo=$userRepo;
    }

    /**
     * @Rest\Get("/api/users/{email}")
     */
    public function getApiUser(User $user)
    {
        return $this->view($user);
    }

    /**
     * @Rest\Get("/api/users")
     */
    public function getApiUsers()
    {
        $users = $this->userRepo->findAll(); 
        return $this->view($users);
    }
    
    /**
     * @ParamConverter("user", converter="fos_rest.request_body")
     * @Rest\Post("api/users/")
     */
    public function postApiUser(User $user, EntityManagerInterface $em)
    {
        $em->persist($user);
        $em->flush();
        return $this->view($user);
    }

    /**
     * @Rest\Delete("api/users/{email}")
     */
    public function deleteApiUser(User $user, EntityManagerInterface $em)
    {
        if($user)
        {
            
            $em->remove($user);
            $em->flush();
            return $this->view("La suppression a bien été effectuée");
        }

    }
}
