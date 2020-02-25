<?php

namespace App\Controller;

use App\Entity\SocialNetwork;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Repository\SocialNetworkRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;
use App\Repository\ProposalRepository;

class SocialNetworkController extends AbstractFOSRestController
{
    private $social;

    static private $postSocialRequiredAttributes = [
        'name' => 'setName',
    ];

    static private $patchSocialModifiableAttributes = [
        'name' => 'setName',
    ];

    public function __construct(SocialNetworkRepository $socialRepo)
    {
        $this->socialRepo=$socialRepo;
    }

    /**
     * @Rest\Get("/api/socials/")
     */
    public function getApiSocials()
    {
        $socials=$this->socialRepo->findAll();
        return $this->view($socials);
    }

    /**
     * @Rest\Get("/api/socials/{id}")
     */
    public function getApiSocial(SocialNetwork $social)
    {
        return $this->view($social);
    }

    /**
     * @Rest\Post("/api/socials/")
     */
    public function postApiSocial(Request $request, EntityManagerInterface $em)
    {
        $social=new SocialNework();

        $em->persist($social);
        $em->flush();

        return $this->view($social);

    }

    /**
     * @Rest\Delete("api/socials/{id}")
     */
    public function deleteApiSocial(SocialNetwork $social, EntityManagerInterface $em)
    {
        if($social)
        {
            $em->remove($social);
            $em->flush();
            return $this->view("La suppression a bien été effectuée");
        }
    }

    /**
     * @Rest\Patch("api/socials/{id}")
     */
    public function patchApiSocial(SocialNetwork $social, Request $request,EntityManagerInterface $em)
    {
        foreach(static::$patchSocialModifiableAttributes as $attribute => $setter) {
            if(is_null($request->get($attribute))) {
                continue;
            }
            $social->$setter($request->get($attribute));
        }
        $em->flush();
        return $this->view($social);
    }

}
