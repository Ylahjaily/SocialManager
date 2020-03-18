<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Entity\Proposal;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Repository\ProposalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;
use Swagger\Annotations as SWG;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProposalController extends AbstractFOSRestController
{
    private $proposalRepo;

    static private $postProposalRequiredAttributes = [
        'title' => 'setTitle',
        'textContent' => 'setTextContent',
    ];

    static private $patchProposalModifiableAttributes = [
        'title' => 'setTitle',
        'textContent' => 'setTextContent',
        'link' => 'setLink',
        'is_published' => 'setIsPublished',
        'date_publication_at' => 'setDatePublicationAt',
    ];


    public function __construct(ProposalRepository $proposalRepo)
    {
        $this->proposalRepo=$proposalRepo;
    }

    /**
     * @Rest\Get("/api/proposals")
     * @SWG\Response(
     *   response = 200,
     *   description = "return list of proposals"
     * )
     */
    public function getApiProposals(SerializerInterface $serializer)
    {
        $proposals=$this->proposalRepo->findAll();

        if(!$proposals) {
            throw new NotFoundHttpException('Proposals do not exist');
        }

        $json = $serializer->serialize(
            $proposals,
            'json', ['groups' => 'proposal']
        );

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent($json);
        $response->setStatusCode(200);
        return $response;

    }

    /**
     * @Rest\Get("/api/communicant/proposals/approved")
     * @SWG\Response(
     *   response = 200,
     *   description = "return list of proposals which have been approved"
     * )
     */
    public function getApiApprovedProposals(SerializerInterface $serializer)
    {

        $proposals=$this->proposalRepo->findApprovedProposals();

        if(!$proposals) {
            throw new NotFoundHttpException('Proposals do not exist');
        }

        $json = $serializer->serialize(
            $proposals,
            'json', ['groups' => 'proposal']
        );

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent($json);
        $response->setStatusCode(200);
        return $response;
    }

    /**
     * @Rest\Get("/api/proposals/unprocessed")
     * @SWG\Response(
     *   response = 200,
     *   description = "return list of proposals which havent been treated"
     * )
     */
    public function getApiUnProcessedProposals(SerializerInterface $serializer)
    {
        $proposals=$this->proposalRepo->findUnProcessedProposals();

        if(!$proposals) {
            throw new NotFoundHttpException('Proposals do not exist');
        }

        $json = $serializer->serialize(
            $proposals,
            'json', ['groups' => 'proposal']
        );

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent($json);
        $response->setStatusCode(200);
        return $response;
    }

    /**
     * @Rest\Get("/api/reviewer/{id}/proposals")
     * @SWG\Parameter(
     *  name = "id",
     *  in = "path",
     *  type = "number",
     *  description = "the ID of the reviewer",
     *  required = true
     * )
     * @SWG\Response(
     *  response = 200,
     *  description = "list of the approved proposals by reviewer"
     * )
     * @SWG\Response(
     *  response = 404,
     *  description = "User doesn't exist"
     * )
     */
    public function getApiApprovedProposalsByReviewer(User $user,SerializerInterface $serializer)
    {
        if(!$user) {
            throw new NotFoundHttpException('This user does not exist');
        }
        $proposals=$this->proposalRepo->findApprovedProposalByReviewer($user);

        if(!$proposals) {
            throw new NotFoundHttpException('Proposal does not exist');
        }

        $json = $serializer->serialize(
            $proposals,
            'json', ['groups' => 'proposal']
        );

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent($json);
        $response->setStatusCode(200);
        return $response;
    }

    /**
     * @Rest\Get("/api/reviewer/{id}/proposals/rejected")
     * @SWG\Parameter(
     *  name = "id",
     *  in = "path",
     *  type = "number",
     *  description = "the ID of the reviewer",
     *  required = true
     * )
     * @SWG\Response(
     *  response = 200,
     *  description = "list of the rejected proposals by reviewer"
     * )
     * @SWG\Response(
     *  response = 404,
     *  description = "User doesn't exist"
     * )
     */
    public function getApiRejectedProposalsByReviewer(User $user, SerializerInterface $serializer)
    {
        if(!$user) {
            throw new NotFoundHttpException('This user does not exist');
        }
        $proposals=$this->proposalRepo->findRejectedProposalsByReviewer($user);

        if(!$proposals) {
            throw new NotFoundHttpException('Proposal does not exist');
        }

        $json = $serializer->serialize(
            $proposals,
            'json', ['groups' => 'proposal']
        );

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent($json);
        $response->setStatusCode(200);
        return $response;
    }

    /**
     * @Rest\Get("/api/member/{id}/proposals/rejected")
     * @Rest\View(serializerGroups={"proposal"})
     * @SWG\Parameter(
     *  name = "id",
     *  in = "path",
     *  type = "number",
     *  description = "the ID of the member",
     *  required = true
     * )
     * @SWG\Response(
     *  response = 200,
     *  description = "list of the rejected proposals by member"
     * )
     * @SWG\Response(
     *  response = 404,
     *  description = "User doesn't exist"
     * )
     */
    public function getApiRejectedProposalsByMember(User $user, SerializerInterface $serializer)
    {
        if(!$user) {
            throw new NotFoundHttpException('This user does not exist');
        }
        $proposals=$this->proposalRepo->findRejectedProposalsByMember($user);

        if(!$proposals) {
            throw new NotFoundHttpException('Proposal does not exist');
        }

        $json = $serializer->serialize(
            $proposals,
            'json', ['groups' => 'proposal']
        );

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent($json);
        $response->setStatusCode(200);
        return $response;
    }

    /**
     * @Rest\Get("/api/proposals/{id}")
     * @SWG\Parameter(
     *  name = "id",
     *  in = "path",
     *  type = "number",
     *  description="The ID of the proposal",
     *  required=true
     * )
     * @SWG\Response(
     *  response = 200,
     *  description = "return one proposal"
     * )
     * @SWG\Response(
     *  response = 404,
     *  description = "proposal not found"
     * )
     */
    public function getApiProposalById(Proposal $proposal)
    {
        return $this->view($proposal);
    }

    /**
     * @Rest\Post("/api/profile/{id}/proposals")
     * @SWG\Parameter(
     *  name = "id",
     *  in = "path",
     *  type = "number",
     *  description = "the id of the User",
     *  required = true
     * )
     * @SWG\Parameter(
     *  name = "title",
     *  in = "body",
     *  type = "string",
     *  description = "the title of the new Proposal",
     *  required = true,
     *  @SWG\Schema(
     *      example = "title -xxx",
     *      type = "text"
     *  )
     * )
     * @SWG\Parameter(
     *  name = "textContent",
     *  in = "body",
     *  type = "string",
     *  description = "the text content of the proposal",
     *  required = true,
     *  @SWG\Schema(
     *      example = "text content -002",
     *      type = "text"
     *  )
     * )
     * @SWG\Response(
     *  response = 201,
     *  description = "Proposal created"
     * )
     * @SWG\Response(
     *  response = 400,
     *  description = "Uncorect request"
     * )
     */
    public function postApiProposal(Request $request, User $user,ValidatorInterface $validator, EntityManagerInterface $em, SerializerInterface $serializer)
    {
        $proposal=new Proposal();

        if(!$user) {
            throw new NotFoundHttpException('This user does not exist');
        }
        $proposal->setUserId($user);

        foreach(static::$postProposalRequiredAttributes as $attribute => $setter) {
            if(is_null($request->get($attribute))) {
                continue;
            }
            $proposal->$setter($request->get($attribute));
        }

        if(!is_null($request->get('link'))) {
            $link = $request->get('link');
            if(preg_match('#^(http|https)://[\w-]+[\w.-]+\.[a-zA-Z]{2,6}#i', $link))
            {
                $proposal->setLink($link);
            }
        }

        $validationErrors = $validator->validate($proposal);

        /** @var ConstraintViolation $constraintViolation */
        foreach($validationErrors as $constraintViolation) {
            $message = $constraintViolation->getMessage();
            $propertyPath = $constraintViolation->getPropertyPath();
            $errors[] = ['property' => $propertyPath, 'message' => $message];
        }

        if(!empty($errors)) {
            return new JsonResponse($errors, 400);
        }

        $em->persist($proposal);
        $em->flush();

        $json = $serializer->serialize(
            $proposal,
            'json', ['groups' => 'proposal']
        );

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent($json);
        $response->setStatusCode(201);
        return $response;
    }

    /**
     * @Rest\Delete("api/proposals/{id}")
     * @SWG\Parameter(
     *  name = "id",
     *  in = "path",
     *  type = "number",
     *  description = "the id of the proposal we want to delete",
     *  required = true
     * )
     * @SWG\Response(
     *  response = 204,
     *  description = "Proposal deleted"
     * )
     * @SWG\Response(
     *  response = 404,
     *  description = "Proposal not found"
     * )
     * @SWG\Response(
     *  response = 403,
     *  description = "User not allowed"
     * )
     */
    public function deleteApiProposal(Proposal $proposal, EntityManagerInterface $em, SerializerInterface $serializer)
    {
        if(!$proposal) {
            throw new NotFoundHttpException('This proposal does not exist');
        }
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent("User deleted");
        $response->setStatusCode(204);
        return $response;
    }

    /**
     * @Rest\Patch("api/reviewer/proposals/{id}")
     * @SWG\Parameter(
     *  name = "id",
     *  in = "path",
     *  type = "number",
     *  description = "the Id of the Proposal",
     *  required = true
     * )
     * @SWG\Parameter(
     *  name = "title",
     *  in = "body",
     *  type = "string",
     *  description = "The title of the Proposal",
     *  required = true,
     *  @SWG\Schema(
     *      example = "title -xxx",
     *      type="string"
     *  )
     * )
     * @SWG\Parameter(
     *  name = "textContent",
     *  in = "body",
     *  type = "text",
     *  description = "The content of the Proposal",
     *  required = true,
     *  @SWG\Schema(
     *      example = "Content -xxx",
     *      type="text"
     *  )
     * )
     * @SWG\Response(
     *  response = 200,
     *  description = "Proposal updated"
     * )
     * @SWG\Response(
     *  response = 403,
     *  description = "User not allowed"
     * )
     * @SWG\Response(
     *  response = 404,
     *  description = "Proposal doesn't exist"
     * )
     */
    public function patchApiProposal(Proposal $proposal, Request $request, ValidatorInterface $validator, EntityManagerInterface $em, SerializerInterface $serializer)
    {
        if(!$proposal) {
            throw new NotFoundHttpException('This proposal does not exist');
        }

        foreach(static::$patchProposalModifiableAttributes as $attribute => $setter) {
            if(is_null($request->get($attribute))) {
                continue;
            }
            $proposal->$setter($request->get($attribute));
        }

        if(!is_null($request->get('link'))) {
            $link = $request->get('link');
            if(preg_match('#^(http|https)://[\w-]+[\w.-]+\.[a-zA-Z]{2,6}#i', $link))
            {
                $proposal->setLink($link);
            }
        }

        $validationErrors = $validator->validate($proposal);

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
            $proposal,
            'json', ['groups' => 'proposal']
        );

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent($json);
        $response->setStatusCode(200);
        return $response;
    }

    /**
     * @Rest\Get("/api/profile/proposals/published")
     * @SWG\Response(
     *   response = 200,
     *   description = "return list of proposals which have been published on social networks"
     * )
     */
    public function getApiPublishedProposals(SerializerInterface $serializer)
    {
        $published_proposals = $this->proposalRepo->findPublishedProposals();

        if(!$published_proposals) {
            throw new NotFoundHttpException('Ther is no published proposal');
        }

        $json = $serializer->serialize(
            $published_proposals,
            'json', ['groups' => 'proposal']
        );

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent($json);
        $response->setStatusCode(200);
        return $response;
    }

    /**
     * @Rest\Get("/api/profile/{id}/published")
     * @SWG\Parameter(
     *  name = "id",
     *  in = "path",
     *  type = "number",
     *  description = "the ID of the user",
     *  required = true
     * )
     * @SWG\Response(
     *  response = 200,
     *  description = "list of the published proposals by User"
     * )
     * @SWG\Response(
     *  response = 404,
     *  description = "User doesn't exist"
     * )
     */
    public function getApiPublishedProposalsByUser(User $user, SerializerInterface $serializer)
    {
        if(!$user) {
            throw new NotFoundHttpException('This user does not exist');
        }
        $published_proposals=$this->proposalRepo->findPublishedProposalsByUser($user);

        if(!$published_proposals) {
            throw new NotFoundHttpException('There is no published proposal by you...');
        }

        $json = $serializer->serialize(
            $published_proposals,
            'json', ['groups' => 'proposal']
        );

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent($json);
        $response->setStatusCode(200);
        return $response;
    }

    /**
     * @Rest\Get("/api/profile/{id}/proposals/approved")
     * @SWG\Parameter(
     *  name = "id",
     *  in = "path",
     *  type = "number",
     *  description = "the ID of the user",
     *  required = true
     * )
     * @SWG\Response(
     *  response = 200,
     *  description = "list of the approved proposals by User"
     * )
     * @SWG\Response(
     *  response = 404,
     *  description = "User doesn't exist"
     * )
     */
    public function getApiApprovedProposalsByMember(User $user, SerializerInterface $serializer)
    {
        if(!$user) {
            throw new NotFoundHttpException('This user does not exist');
        }
        $proposals=$this->proposalRepo->findApprovedProposalsByMember($user);

        if(!$proposals) {
            throw new NotFoundHttpException('There is no approved proposal');
        }

        $json = $serializer->serialize(
            $proposals,
            'json', ['groups' => 'proposal']
        );

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent($json);
        $response->setStatusCode(200);
        return $response;
    }
}
