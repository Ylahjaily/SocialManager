<?php

namespace App\Controller;

use App\Entity\UploadedDocument;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Repository\UploadedDocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ProposalRepository;

class UploadedDocumentController extends AbstractFOSRestController
{
    private $uploadedDocRepo;

    static private $postUploadedDocumentRequiredAttributes = [
        'title' => 'setTitle',
        'data' => 'setData',
    ];

    static private $patchUploadedDocumentModifiableAttributes = [
        'title' => 'setTitle'
    ];

    public function __construct(UploadedDocumentRepository $uploadedDocRepo)
    {
        $this->uploadedDocRepo=$uploadedDocRepo;
    }

    /**
     * @Rest\Get("/api/up_docs/")
     */
    public function getApiUploadedDocuments()
    {
        $uploaded_documents=$this->uploadedDocRepo->findAll();
        return $this->view($uploaded_documents);
    }

    /**
     * @Rest\Get("/api/up_docs/{id}")
     */
    public function getApiUploadedDocument(UploadedDocument $uploadedDocument)
    {
        return $this->view($uploadedDocument);
    }

    /**
     * @Rest\Post("/api/up_docs/")
     */
    public function postApiUploadedDocument(Request $request, ProposalRepository $proposalRepo, EntityManagerInterface $em)
    {
        $uploadedDocument=new UploadedDocument();

        foreach(static::$postUploadedDocumentRequiredAttributes as $attribute => $setter) {
            if(is_null($request->get($attribute))) {
                continue;
            }
            $uploadedDocument->$setter($request->get($attribute));
        }

        if(!is_null($request->get('proposal_id'))) {
            $proposal = $proposalRepository->find($request->get('proposal_id'));
            if(!is_null($proposal)) {
                $uploadedDocument->setProposalId($proposal);
            }
        }

        $em->persist($uploadedDocument);
        $em->flush();

        return $this->view($uploadedDocument);

    }

    /**
     * @Rest\Delete("api/up_docs/{id}")
     */
    public function deleteApiUploadedDocument(UploadedDocument $uploadedDocument, EntityManagerInterface $em)
    {
        if($uploadedDocument)
        {
            $em->remove($uploadedDocument);
            $em->flush();
            return $this->view("La suppression a bien été effectuée");
        }
    }

    /**
     * @Rest\Patch("api/up_docs/{id}")
     */
    public function patchApiUploadedDocument(UploadedDocument $uploadedDocument, Request $request,EntityManagerInterface $em)
    {
        foreach(static::$patchUploadedDocumentModifiableAttributes as $attribute => $setter) {
            if(is_null($request->get($attribute))) {
                continue;
            }
            $uploadedDocument->$setter($request->get($attribute));
        }
        $em->flush();
        return $this->view($uploadedDocument);
    }

}
