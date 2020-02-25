<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Entity\Proposal;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Repository\ProposalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;

class ProposalController extends AbstractFOSRestController
{
    private $proposalRepo;

    static private $postProposalRequiredAttributes = [
        'title' => 'setTitle',
        'textContent' => 'setTextContent',
    ];

    public function __construct(ProposalRepository $proposalRepo)
    {
        $this->proposalRepo=$proposalRepo;
    }

    /**
     * @Rest\Get("/api/proposals")
     */
    public function getApiProposals()
    {
        $proposals=$this->proposalRepo->findAll();
        return $this->view($proposals);
    }

    /**
     * @Rest\Get("/api/proposals/approved")
     */
    public function getApiApprovedProposals()
    {
        $proposals=$this->proposalRepo->findApprovedProposals();
        return $this->view($proposals);
    }

    /**
     * @Rest\Get("/api/proposals/unprocessed")
     */
    public function getApiUnProcessedProposals()
    {
        $proposals=$this->proposalRepo->findUnProcessedProposals();
        return $this->view($proposals);
    }

    /**
     * @Rest\Get("/api/reviewer/{id}/proposals")
     */
    public function getApiApprovedProposalsByReviewer(User $user)
    {
        if(!$user) {
            throw new NotFoundHttpException('This user does not exist');
        }
            $proposals=$this->proposalRepo->findApprovedProposalByReviewer($user);

        if(!$proposals) {
            throw new NotFoundHttpException('Proposal does not exist');
        }
            return $this->view($proposals);
    }

    /**
     * @Rest\Get("/api/proposals/{id}")
     */
    public function getApiProposalById(Proposal $proposal)
    {
        return $this->view($proposal);
    }

    /**
     * @Rest\Post("/api/proposals")
     */
    public function postApiProposal(Request $request, UserRepository $userRepository, EntityManagerInterface $em)
    {
        $proposal=new Proposal();

        foreach(static::$postProposalRequiredAttributes as $attribute => $setter) {
            if(is_null($request->get($attribute))) {
                continue;
            }
            $proposal->$setter($request->get($attribute));
        }

        if(!is_null($request->get('user_id'))) {
            $user = $userRepository->find($request->get('user_id'));
            if(!is_null($user)) {
                $proposal->setUserId($user);
            }
        }

        $em->persist($proposal);
        $em->flush();

        return $this->view($proposal);

    }

    /**
     * @Rest\Delete("api/proposals/{id}")
     */
    public function deleteApiProposal(Proposal $proposal, EntityManagerInterface $em)
    {
        if($proposal)
        {
            $em->remove($proposal);
            $em->flush();
            return $this->view("La suppression a bien été effectuée");
        }

    }

}
