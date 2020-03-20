<?php

namespace App\Controller;

use App\Entity\User;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Entity\Proposal;
use Swagger\Annotations as SWG;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
    public function getApiUser(User $user,SerializerInterface $serializer)
    {

        if(!$user) {
            throw new NotFoundHttpException('This user does not exist');
        }

        $json = $serializer->serialize(
            $user,
            'json', ['groups' => 'user']
        );

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setStatusCode(200);
        $response->setContent($json);
        return $response;
    }


    /**
     * @Rest\Get("/api/users/")
     * @SWG\Response(
     *   response = 200,
     *   description = "return list of users"
     * )
     */
    public function getApiUsers(SerializerInterface $serializer)
    {
        $users = $this->userRepo->findAll();

        $json = $serializer->serialize(
            $users,
            'json', ['groups' => 'user']
        );

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setStatusCode(200);
        $response->setContent($json);
        return $response;
    }

    /**
     * @Rest\Post("api/users/")
     * @ParamConverter("user", converter="fos_rest.request_body")
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
    public function postApiUser(User $user, ValidatorInterface $validator, EntityManagerInterface $em, SerializerInterface $serializer)
    {
        $validationErrors = $validator->validate($user);

        /** @var ConstraintViolation $constraintViolation */
        foreach($validationErrors as $constraintViolation) {
            $message = $constraintViolation->getMessage();
            $propertyPath = $constraintViolation->getPropertyPath();
            $errors[] = ['property' => $propertyPath, 'message' => $message];
        }

        if(!empty($errors)) {
            return new JsonResponse($errors, 400);
        }

        $em->persist($user);
        $em->flush();

        $json = $serializer->serialize(
            $user,
            'json', ['groups' => 'user']
        );

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent($json);
        $response->setStatusCode(201);
        return $response;
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
        if(!$user) {
            throw new NotFoundHttpException('This user does not exist');
        }
            $em->remove($user);
            $em->flush();

            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->headers->set('Access-Control-Allow-Origin', '*');
            $response->setContent("User deleted");
            $response->setStatusCode(204);
            return $response;
    }

    /**
     * @Rest\Patch("/api/admin/users/{id}")
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
    public function patchApiUser(User $user, Request $request, ValidatorInterface $validator, EntityManagerInterface $em, SerializerInterface $serializer)
    {
        if(!$user) {
            throw new NotFoundHttpException('This user does not exist');
        }

        foreach(static::$patchUserModifiableAttributes as $attribute => $setter) {
            if(is_null($request->get($attribute))) {
                continue;
            }
            $user->$setter($request->get($attribute));
        }

        $validationErrors = $validator->validate($user);

        /** @var ConstraintViolation $constraintViolation */
        foreach($validationErrors as $constraintViolation) {
            $message = $constraintViolation->getMessage();
            $propertyPath = $constraintViolation->getPropertyPath();
            $errors[] = ['property' => $propertyPath, 'message' => $message];
        }

        if(!empty($errors)) {
            return new JsonResponse($errors, 400);
        }

        $em->flush();
        $json = $serializer->serialize(
            $user,
            'json', ['groups' => 'user']
        );

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent($json);
        $response->setStatusCode(200);
        return $response;
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
    public function getReviewersByProposal(Proposal $proposal,SerializerInterface $serializer)
    {
        if(!$proposal) {
            throw new NotFoundHttpException('This proposal does not exist');
        }
        $reviewers=$this->userRepo->findReviewersByProposal($proposal);

        if(!$reviewers) {
            throw new NotFoundHttpException('Reviewers does not exists for this review');
        }

        $json = $serializer->serialize(
            $proposal,
            'json', ['groups' => 'user']
        );

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent($json);
        $response->setStatusCode(200);
        return $response;
    }
}
