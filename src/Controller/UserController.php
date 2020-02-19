<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Repository\UserRepository;

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

    }

    /**
     * @Rest\Get("/api/users")
     */
    public function getApiUsers()
    {
        $users = $this->userRepo->findAll(); 
        return $this->view($users);
    }  
}
