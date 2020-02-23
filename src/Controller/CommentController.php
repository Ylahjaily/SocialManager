<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Comment;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Repository\CommentRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use App\Repository\ProposalRepository;
use Symfony\Component\HttpFoundation\Request;

class CommentController extends AbstractFOSRestController
{
    private $commentRepo;

    static private $postCommentRequiredAttributes = [
        'content' => 'setContent',
    ];

    public function __construct(CommentRepository $commentRepo)
    {
        $this->commentRepo=$commentRepo;
    }

    /**
     * @Rest\Get("/api/comments/")
     */
    public function getApiComments()
    {
        $comments=$this->commentRepo->findAll();
        return $this->view($comments);
    }

    /**
     * @Rest\Get("/api/comments/{id}")
     */
    public function getApiComment(Comment $comment)
    {
        return $this->view($comment);
    }

    /**
     * @Rest\Post("/api/comments/")
     */
    public function postApiComment(Request $request, ProposalRepository $proposalRepository, UserRepository $userRepository, EntityManagerInterface $em)
    {
        $comment=new Comment();

        foreach(static::$postCommentRequiredAttributes as $attribute => $setter) {
            if(is_null($request->get($attribute))) {
                continue;
            }
            $comment->$setter($request->get($attribute));
        }

        if(!is_null($request->get('proposal_id'))) {
            $proposal = $proposalRepository->find($request->get('proposal_id'));
            
            if(!is_null($proposal)) {
                $comment->setProposalId($proposal);
            }
        }

        if(!is_null($request->get('user_id'))) {
            $user = $userRepository->find($request->get('user_id'));
            if(!is_null($user)) {
                $comment->setUserId($user);
            }
        }
        $em->persist($comment);
        $em->flush();

        return $this->view($comment);
    
    }

    /**
     * @Rest\Delete("api/comments/{id}")
     */
    public function deleteApiComment(Comment $comment, EntityManagerInterface $em)
    {
        if($comment)
        {      
            $em->remove($comment);
            $em->flush();
            return $this->view("La suppression a bien été effectuée");
        }

    }

}
