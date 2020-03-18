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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
     * @Rest\View(serializerGroups={"uploadedDoc"})
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
     * @Rest\View(serializerGroups={"uploadedDoc"})
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
     * @Rest\Get("/api/communicant/uploaded_docs/approved")
     * @Rest\View(serializerGroups={"uploadedDoc"})
     * @SWG\Response(
     *   response = 200,
     *   description = "return list of files which have been approved"
     * )
     */
    public function getApiApprovedFiles()
    {
        $up_docs=$this->uploadedDocRepo->findApprovedFiles();
        if(!$up_docs) {
            throw new NotFoundHttpException('There is no approved files yet');
        }
        return $this->view($up_docs);

    }

    /**
     * @Rest\Get("/api/uploaded_docs/unprocessed")
     * @Rest\View(serializerGroups={"uploadedDoc"})
     * @SWG\Response(
     *   response = 200,
     *   description = "return list of files which havent been treated"
     * )
     */
    public function getApiUnProcessedFiles()
    {
        $up_docs=$this->uploadedDocRepo->findUnProcessedFiles();
        return $this->view($up_docs);
    }

    /**
     * @Rest\Get("/api/reviewer/{id}/up_docs")
     * @Rest\View(serializerGroups={"uploadedDoc"})
     * @SWG\Parameter(
     *  name = "id",
     *  in = "path",
     *  type = "number",
     *  description = "the ID of the reviewer",
     *  required = true
     * )
     * @SWG\Response(
     *  response = 200,
     *  description = "list of the approved files by reviewer"
     * )
     * @SWG\Response(
     *  response = 404,
     *  description = "User doesn't exist"
     * )
     */
    public function getApiApprovedFilesByReviewer(User $user)
    {
        if(!$user) {
            throw new NotFoundHttpException('This user does not exist');
        }
        $up_docs=$this->uploadedDocRepo->findApprovedFilesByReviewer($user);

        if(!$up_docs) {
            throw new NotFoundHttpException('There is no file approved by this reviewer');
        }
        return $this->view($up_docs);
    }
    
    /**
     * @Rest\Get("/api/reviewer/{id}/up_docs/rejected")
     * @Rest\View(serializerGroups={"uploadedDoc"})
     * @SWG\Parameter(
     *  name = "id",
     *  in = "path",
     *  type = "number",
     *  description = "the ID of the reviewer",
     *  required = true
     * )
     * @SWG\Response(
     *  response = 200,
     *  description = "list of the rejected files by reviewer"
     * )
     * @SWG\Response(
     *  response = 404,
     *  description = "User doesn't exist"
     * )
     */
    public function getApiRejectedFilesByReviewer(User $user)
    {
        if(!$user) {
            throw new NotFoundHttpException('This user does not exist');
        }
        $up_docs=$this->uploadedDocRepo->findRejectedFilesByReviewer($user);

        if(!$up_docs) {
            throw new NotFoundHttpException('There is no file here...');
        }
        return $this->view($up_docs);
    }

    /**
     * @Rest\Get("/api/member/{id}/up_docs/rejected")
     * @Rest\View(serializerGroups={"uploadedDoc"})
     * @SWG\Parameter(
     *  name = "id",
     *  in = "path",
     *  type = "number",
     *  description = "the ID of the member",
     *  required = true
     * )
     * @SWG\Response(
     *  response = 200,
     *  description = "list of the rejected files by member"
     * )
     * @SWG\Response(
     *  response = 404,
     *  description = "User doesn't exist"
     * )
     */
    public function getApiRejectedFilesByMember(User $user)
    {
        if(!$user) {
            throw new NotFoundHttpException('This user does not exist');
        }
        $up_docs=$this->uploadedDocRepo->findRejectedFilesByMember($user);

        if(!$up_docs) {
            throw new NotFoundHttpException('Files do not exist');
        }
        return $this->view($up_docs);
    }

    /**
     * @Rest\Get("/api/profile/up_docs/published")
     * @Rest\View(serializerGroups={"uploadedDoc"})
     * @SWG\Response(
     *   response = 200,
     *   description = "return list of files which have been published on social networks"
     * )
     */
    public function getApiPublishedProposals()
    {
        $up_docs = $this->uploadedDocRepo->findPublishedFiles();

        if(!$up_docs) {
            throw new NotFoundHttpException('There is no published file');
        }

        return $this->view($up_docs);
    }

    /**
     * @Rest\Get("/api/profile/{id}/up_docs/published")
     * @Rest\View(serializerGroups={"uploadedDoc"})
     * @SWG\Parameter(
     *  name = "id",
     *  in = "path",
     *  type = "number",
     *  description = "the ID of the user",
     *  required = true
     * )
     * @SWG\Response(
     *  response = 200,
     *  description = "list of the published files by User"
     * )
     * @SWG\Response(
     *  response = 404,
     *  description = "User doesn't exist"
     * )
     */
    public function getApiPublishedFilesByUser(User $user)
    {
        if(!$user) {
            throw new NotFoundHttpException('This user does not exist');
        }
        $up_docs=$this->uploadedDocRepo->findPublishedFilesByUser($user);

        if(!$up_docs) {
            throw new NotFoundHttpException('There is no published file by you...');
        }
        return $this->view($up_docs);
    }

        /**
     * @Rest\Get("/api/profile/{id}/up_docs/approved")
     * @Rest\View(serializerGroups={"uploadedDocument"})
     * @SWG\Parameter(
     *  name = "id",
     *  in = "path",
     *  type = "number",
     *  description = "the ID of the user",
     *  required = true
     * )
     * @SWG\Response(
     *  response = 200,
     *  description = "list of the approved files by User"
     * )
     * @SWG\Response(
     *  response = 404,
     *  description = "User doesn't exist"
     * )
     */
    public function getApiApprovedFilesByMember(User $user)
    {
        if(!$user) {
            throw new NotFoundHttpException('This user does not exist');
        }
        $up_docs=$this->uploadedDocRepo->findApprovedFilesByMember($user);

        if(!$up_docs) {
            throw new NotFoundHttpException('There is no approved file by you');
        }
        return $this->view($up_docs);
    }

    /**
     * @Rest\Post("/api/users/{id}/up_docs/")
     * @Rest\View(serializerGroups={"uploadedDoc"})
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
     * @Rest\View(serializerGroups={"uploadedDoc"})
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
     * @Rest\View(serializerGroups={"uploadedDoc"})
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
