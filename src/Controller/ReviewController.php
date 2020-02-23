<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Review;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Repository\ReviewRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Doctrine\ORM\EntityManagerInterface;

class ReviewController extends AbstractFOSRestController
{
    private $reviewRepo;

    public function __construct(ReviewRepository $reviewRepo)
    {
        $this->reviewRepo=$reviewRepo;
    }

    /**
     * @Rest\Get("/api/reviews/")
     */
    public function getApiReviews()
    {
        $reviews=$this->reviewRepo->findAll();
        return $this->view($reviews);
    }
    
    /**
     * @Rest\Get("/api/reviews/{id}")
     */
    public function getApiReview(Review $review)
    {
        return $this->view($review);
    }


}
