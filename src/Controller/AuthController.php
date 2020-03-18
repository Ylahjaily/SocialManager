<?php


namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthController extends AbstractFOSRestController
{
    private $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo=$userRepo;
    }

    /**
     * @Rest\Post("/api/auth/login", path="/api/auth/login", name="app_auth_login")
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils,UserPasswordEncoderInterface $passwordEncoder, SerializerInterface $serializer)
    {
        // if ($this->getUser()) {
        //    $this->redirectToRoute('target_path');
        // }


        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $json = $serializer->serialize(
            $lastUsername,
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
     * @Rest\Post("/auth/register", name="app_register")
     * @ParamConverter("user", converter="fos_rest.request_body")
     */
    public function register(User $user, ValidatorInterface $validator, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $em,SerializerInterface $serializer)
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
        $password = $passwordEncoder->encodePassword($user, $user->getPassword());
        $user->setPassword($password);
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
}
