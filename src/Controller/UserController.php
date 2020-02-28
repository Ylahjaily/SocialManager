<?php

namespace App\Controller;

use App\Entity\User;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractFOSRestController
{
    private $userRepo;
    
    static private $patchUserModifiableAttributes = [
        'lastName' => 'setLastName',
        'firstName' => 'setFirstName',
        'roles' => 'setRoles',
        'apiKey' => 'setApiKey',
        'password' => 'setPassword',
    ];

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo=$userRepo;
    }

    /**
     * @Rest\Get("/api/users/{email}")
     * @Rest\View(serializerGroups={"user"})
     */
    public function getApiUser(User $user)
    {
        return $this->view($user);
    }

    /**
     * @Rest\Get("/api/users")
     * @Rest\View(serializerGroups={"user"})
     */
    public function getApiUsers()
    {
        $users = $this->userRepo->findAll();
        return $this->view($users);
    }

    /**
     * @ParamConverter("user", converter="fos_rest.request_body")
     * @Rest\Post("api/users/")
     * @Rest\View(serializerGroups={"user"})
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

    /**
     * @Rest\Patch("api/users/{id}")
     * @Rest\View(serializerGroups={"user"})
     */
    public function patchApiUser(User $user, Request $request,EntityManagerInterface $em)
    {
        foreach(static::$patchUserModifiableAttributes as $attribute => $setter) {
            if(is_null($request->get($attribute))) {
                continue;
            }
            $user->$setter($request->get($attribute));
        }
        $em->flush();
        return $this->view($user);
    }
}
