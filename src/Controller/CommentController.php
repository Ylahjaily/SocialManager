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

class CommentController extends AbstractFOSRestController
{
    private $commentRepo;

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

}
