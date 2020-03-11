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
use Swagger\Annotations as SWG;

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
     * @Rest\View(serializerGroups={"social"})
     * @SWG\Response(
     *   response = 200,
     *   description = "return list of social networks"
     * )
     */
    public function getApiSocials()
    {
        $socials=$this->socialRepo->findAll();
        return $this->view($socials);
    }

    /**
     * @Rest\Get("/api/socials/{id}")
     * @Rest\View(serializerGroups={"social"})
     * @SWG\Parameter(
     *  name = "id",
     *  in = "path",
     *  type = "number",
     *  description="The ID of the social network",
     *  required=true
     * )
     * @SWG\Response(
     *  response = 200,
     *  description = "return one social network"
     * )
     * @SWG\Response(
     *  response = 404,
     *  description = "social network not found"
     * )
     */
    public function getApiSocial(SocialNetwork $social)
    {
        return $this->view($social);
    }

    /**
     * @Rest\Post("/api/admin/socials/")
     * @Rest\View(serializerGroups={"social"})
     * @SWG\Parameter(
     *  name = "name",
     *  in = "body",
     *  type = "string",
     *  description = "the name of the social network",
     *  required = true,
     *  @SWG\Schema(
     *      example = "Twitter",
     *      type = "string"
     *  )
     * )
     * @SWG\Response(
     *  response = 201,
     *  description = "Social Network created"
     * )
     * @SWG\Response(
     *  response = 400,
     *  description = "Uncorect request"
     * )
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
     * @Rest\View(serializerGroups={"social"})
     * @SWG\Parameter(
     *  name = "id",
     *  in = "path",
     *  type = "number",
     *  description = "the id of the Social Network we want to delete",
     *  required = true
     * )
     * @SWG\Response(
     *  response = 204,
     *  description = "Social Network deleted"
     * )
     * @SWG\Response(
     *  response = 404,
     *  description = "Social Network not found"
     * )
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
     * @Rest\View(serializerGroups={"social"})
     * @SWG\Parameter(
     *  name = "id",
     *  in = "path",
     *  type = "number",
     *  description = "the Id of the Social Network",
     *  required = true
     * )
     * @SWG\Parameter(
     *  name = "name",
     *  in = "body",
     *  type = "string",
     *  description = "The name of the social network",
     *  required = true,
     *  @SWG\Schema(
     *      example = "twitter",
     *      type="string"
     *  )
     * )
     * @SWG\Response(
     *  response = 200,
     *  description = "Social Network updated"
     * )
     * @SWG\Response(
     *  response = 403,
     *  description = "User not allowed"
     * )
     * @SWG\Response(
     *  response = 404,
     *  description = "Social network doesn't exist"
     * )
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
