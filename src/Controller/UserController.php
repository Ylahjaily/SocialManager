<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

class UserController extends AbstractFOSRestController
{
    /**
     * @Rest\Get("/api/users/{email}")
     */
    public function getApiUser(User $user)
    {
        //TODO
    }

    /**
     * @Rest\Get("/api/users")
     */
    public function getApiUsers()
    {
        //TODO
    }  
}
