<?php

namespace App\Controller;

use App\Entity\UploadedDocument;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Repository\UploadedDocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ProposalRepository;
use Swagger\Annotations as SWG;
use App\Entity\Proposal;

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
     * @SWG\Response(
     *   response = 200,
     *   description = "return list of documents"
     * )
     */
    public function getApiUploadedDocuments()
    {
        $uploaded_documents=$this->uploadedDocRepo->findAll();
        return $this->view($uploaded_documents);
    }

    /**
     * @Rest\Get("/api/up_docs/{id}")
     * @SWG\Parameter(
     *  name = "id",
     *  in = "path",
     *  type = "number",
     *  description="The ID of the document",
     *  required=true
     * )
     * @SWG\Response(
     *  response = 200,
     *  description = "return one document"
     * )
     * @SWG\Response(
     *  response = 404,
     *  description = "document not found"
     * )
     */
    public function getApiUploadedDocument(UploadedDocument $uploadedDocument)
    {
        return $this->view($uploadedDocument);
    }

    /**
     * @Rest\Post("/api/proposals/{id}/up_docs/")
     * @SWG\Parameter(
     *  name = "id",
     *  in = "path",
     *  type = "number",
     *  description = "the id of the proposal",
     *  required = true
     * )
     * @SWG\Parameter(
     *  name = "title",
     *  in = "body",
     *  type = "string",
     *  description = "the title of the document which will be added to the proposal",
     *  required = true,
     *  @SWG\Schema(
     *      example = "document 1",
     *      type = "string"
     *  )
     * )
     * @SWG\Parameter(
     *  name = "data",
     *  in = "body",
     *  type = "file",
     *  description = "the file of the document which will be added to the proposal",
     *  required = true,
     *  @SWG\Schema(
     *      example = "document.pdf",
     *      type = "file"
     *  )
     * )
     * @SWG\Response(
     *  response = 201,
     *  description = "Document added"
     * )
     * @SWG\Response(
     *  response = 400,
     *  description = "Uncorect request"
     * )
     */
    public function postApiUploadedDocument(Request $request, Proposal $proposal, EntityManagerInterface $em)
    {
        $uploadedDocument=new UploadedDocument();

        if(!$proposal) {
            throw new NotFoundHttpException('This proposal does not exist');
        }
        $uploadedDocument->setProposalId($proposal);

        foreach(static::$postUploadedDocumentRequiredAttributes as $attribute => $setter) {
            if(is_null($request->get($attribute))) {
                continue;
            }
            $uploadedDocument->$setter($request->get($attribute));
        }


        $em->persist($uploadedDocument);
        $em->flush();

        return $this->view($uploadedDocument);

    }

    /**
     * @Rest\Delete("api/up_docs/{id}")
     * @SWG\Parameter(
     *  name = "id",
     *  in = "path",
     *  type = "number",
     *  description = "the id of the document we want to delete",
     *  required = true
     * )
     * @SWG\Response(
     *  response = 204,
     *  description = "Document deleted"
     * )
     * @SWG\Response(
     *  response = 403,
     *  description = "User not allowed"
     * )
     * @SWG\Response(
     *  response = 404,
     *  description = "Document not found"
     * )
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
     * @SWG\Parameter(
     *  name = "id",
     *  in = "path",
     *  type = "number",
     *  description = "the Id of the document",
     *  required = true
     * )
     * @SWG\Parameter(
     *  name = "title",
     *  in = "body",
     *  type = "string",
     *  description = "The title of the doc",
     *  required = true,
     *  @SWG\Schema(
     *      example = "title -001",
     *      type="string"
     *  )
     * )
     * @SWG\Response(
     *  response = 200,
     *  description = "Document updated"
     * )
     * @SWG\Response(
     *  response = 403,
     *  description = "User not allowed"
     * )
     * @SWG\Response(
     *  response = 404,
     *  description = "Document doesn't exist"
     * )
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
