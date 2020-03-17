<?php

namespace App\Controller;

use App\Entity\UploadedDocument;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Repository\UploadedDocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Swagger\Annotations as SWG;
use App\Entity\User;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\FileParam;
use Symfony\Component\Validator\Constraints;

class UploadedDocumentController extends AbstractFOSRestController
{
    private $uploadedDocRepo;


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
     * @Rest\Post("/api/users/{id}/up_docs/")
     * @Rest\FileParam(name = "image", description = "the media we wwant to upload", nullable=false,image=true)
     * @param ParamFetcher $paramFetcher
     * @SWG\Parameter(
     *  name = "id",
     *  in = "path",
     *  type = "number",
     *  description = "the ID of the User who submits the file"
     * )
     * @SWG\Parameter(
     *  name = "title",
     *  in = "body",
     *  type = "string",
     *  description = "the title of the document which will be added",
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
     *  description = "the file of the document which will be added",
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
     * 
     */
    public function postApiUploadedDocument(ParamFetcher $paramFetcher, User $user, EntityManagerInterface $em, Request $request)
    {
        $uploadedDocument=new UploadedDocument();

        if(!$user) {
            throw new NotFoundHttpException('This user does not exist');
        }
        $uploadedDocument->setUserId($user);

        $file = $paramFetcher->get('image');
        if($file)
        {
            $fileName = md5(uniqid()) . '.' . $file->guessClientExtension();

            $file->move(
                $this->getUploadsDir(),
                $fileName
            );

            $uploadedDocument->setData($fileName);
            $uploadedDocument->setDataPath('/uploads/' . $fileName);

            if($request->get('title'))
            {
                $uploadedDocument->setTitle($request->get('title'));
            }

            $em->persist($uploadedDocument);
            $em->flush();

            $data = $request->getUriForPath(
                $uploadedDocument->getDataPath()
            );
        }

        return $this->view($uploadedDocument);

    }

    private function getUploadsDir()
    {
        return $this->getParameter('uploads_dir');
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
