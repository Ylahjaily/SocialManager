<?php

namespace App\Controller;

use App\Entity\User;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Entity\Proposal;
use Swagger\Annotations as SWG;

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
     * @SWG\Parameter(
     *  name = "email",
     *  in = "path",
     *  type = "string",
     *  description="The email of the user",
     *  required=true
     * )
     * @SWG\Response(
     *  response = 200,
     *  description = "return one user"
     * )
     * @SWG\Response(
     *  response = 404,
     *  description = "user not found"
     * )
     */
    public function getApiUser(User $user)
    {
        return $this->view($user);
    }

    /**
     * @Rest\Get("/api/users/")
     * @Rest\View(serializerGroups={"user"})
     * @SWG\Response(
     *   response = 200,
     *   description = "return list of users"
     * )
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
     * @SWG\Parameter(
     *  name = "email",
     *  in = "body",
     *  type = "string",
     *  description = "the email of the new User",
     *  required = true,
     *  @SWG\Schema(
     *      example = "toto@gmail.com",
     *      type = "string",
     *      maxLength = 255
     *  )
     * )
     * @SWG\Parameter(
     *  name = "lastName",
     *  in = "body",
     *  type = "string",
     *  description = "The last name of the User",
     *  required = true,
     *  @SWG\Schema(
     *      example = "toto",
     *      type="string",
     *      minLength = 2,
     *      maxLength= 50
     *  )
     * )
     * @SWG\Parameter(
     *  name = "firstName",
     *  in = "body",
     *  type = "string",
     *  description = "The first name of the User",
     *  required = true,
     *  @SWG\Schema(
     *      example = "toto",
     *      type = "string",
     *      minLength = 2,
     *      maxLength = 50
     *  )
     * )
     * @SWG\Parameter(
     *  name = "apiKey",
     *  in = "body",
     *  type = "string",
     *  description = "The apiKey of the User",
     *  required = true,
     *  @SWG\Schema(
     *      example = "xUh5Dcx",
     *      type = "string",
     *      maxLength = 255
     *  )
     * )
     * @SWG\Parameter(
     *  name = "password",
     *  in = "body",
     *  type = "string",
     *  description = "The password of the User",
     *  required = true,
     *  @SWG\Schema(
     *      example = "frpgdsc15cd",
     *      type = "string",
     *      minLength = 6,
     *      maxLength = 16
     *  )
     * )
     * @SWG\Response(
     *  response = 201,
     *  description = "User created"
     * )
     * @SWG\Response(
     *  response = 400,
     *  description = "uncorrect request"
     * )
     */
    public function postApiUser(User $user, EntityManagerInterface $em)
    {
        $em->persist($user);
        $em->flush();
        return $this->view($user);
    }

    /**
     * @Rest\Delete("api/users/{id}")
     * @SWG\Parameter(
     *  name = "id",
     *  in = "path",
     *  type = "number",
     *  description = "the id of the user we want to delete",
     *  required = true
     * )
     * @SWG\Response(
     *  response = 204,
     *  description = "User deleted"
     * )
     * @SWG\Response(
     *  response = 403,
     *  description = "User not allowed"
     * )
     * @SWG\Response(
     *  response = 404,
     *  description = "User not found"
     * )
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
     * @Rest\Patch("/api/admin/users/{id}")
     * @Rest\View(serializerGroups={"user"})
     * @SWG\Parameter(
     *  name = "id",
     *  in = "path",
     *  type = "number",
     *  description = "the Id of the User",
     *  required = true
     * )
     * @SWG\Parameter(
     *  name = "lastName",
     *  in = "body",
     *  type = "string",
     *  description = "The last name of the User",
     *  required = true,
     *  @SWG\Schema(
     *      example = "toto",
     *      type="string",
     *      minLength = 2,
     *      maxLength= 50
     *  )
     * )
     * @SWG\Parameter(
     *  name = "firstName",
     *  in = "body",
     *  type = "string",
     *  description = "The first name of the User",
     *  required = true,
     *  @SWG\Schema(
     *      example = "toto",
     *      type = "string",
     *      minLength = 2,
     *      maxLength = 50
     *  )
     * )
     * @SWG\Parameter(
     *  name = "roles",
     *  in = "body",
     *  type = "simple_array",
     *  description = "The role of the User",
     *  required = true,
     *  @SWG\Schema(
     *      example = "array(ROLE_USER)",
     *      type = "simple_array"
     *  )
     * )
     * @SWG\Parameter(
     *  name = "apiKey",
     *  in = "body",
     *  type = "string",
     *  description = "The apiKey of the User",
     *  required = true,
     *  @SWG\Schema(
     *      example = "xUh5Dcx",
     *      type = "string",
     *      maxLength = 255
     *  )
     * )
     * @SWG\Parameter(
     *  name = "password",
     *  in = "body",
     *  type = "string",
     *  description = "The passwword of the User",
     *  required = true,
     *  @SWG\Schema(
     *      example = "frpgdsc15cd",
     *      type = "string",
     *      minLength = 6,
     *      maxLength = 16
     *  )
     * )
     * @SWG\Response(
     *  response = 200,
     *  description = "User updated"
     * )
     * @SWG\Response(
     *  response = 400,
     *  description = "Uncorrect request"
     * )
     * @SWG\Response(
     *  response = 403,
     *  description = "User doesn't have the permission"
     * )
     * @SWG\Response(
     *  response = 404,
     *  description = "User doesn't exist"
     * )
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

    /**
     * @Rest\Get("/api/users/proposals/{id}/reviewers")
     * @SWG\Parameter(
     *  name = "id",
     *  in = "path",
     *  type = "number",
     *  description = "the ID of the proposal",
     *  required = true 
     * )
     * @SWG\Response(
     *  response = 200,
     *  description = "list of the reviewers by proposal"
     * )
     * @SWG\Response(
     *  response = 404,
     *  description = "Proposal not found"
     * )
     */
    public function getReviewersByProposal(Proposal $proposal)
    {
        if(!$proposal) {
            throw new NotFoundHttpException('This proposal does not exist');
        }
        $reviewers=$this->userRepo->findReviewersByProposal($proposal);

        if(!$reviewers) {
            throw new NotFoundHttpException('Reviewers does not exist');
        }
        return $this->view($reviewers);
    }
}
