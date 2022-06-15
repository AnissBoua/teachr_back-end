<?php

namespace App\Controller;

use App\Entity\Teachr;
use App\Repository\TeachrRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TeachrController extends AbstractController
{
    #[Route(path: '/getteachrs', name: 'getteachrs', methods: ['GET'])]
    public function getTeachrs(TeachrRepository $teachrRepository)
    {

        $teachrs = $teachrRepository->findAll();

        foreach ($teachrs as $key => $teachr) {

            $teachrs[$key] = array(
                'id' => $teachr->getId(),
                'prenom' => $teachr->getPrenom(),
                'formation' => $teachr->getFormation(),
                'description' => $teachr->getDescription(),
                'photoURL' => $teachr->getPhoto(),
            );
        }

        return new JsonResponse($teachrs, 200);
    }

    #[Route(path: '/newteachr', name: 'newtearch', methods: ['POST'])]
    public function newTeachr(Request $request, ManagerRegistry $doctrine)
    {
        $em = $doctrine->getManager();
        $data = json_decode($request->getContent(), true);
        //#[UploadedFile $UploadedFile]

        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $request->files->get('photo');
        $destination = $this->getParameter('kernel.project_dir') . '/public/photos';
        $fileName = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $newFileName = $fileName . '-' . uniqid() . '.' . $uploadedFile->guessExtension();
        $uploadedFile->move($destination, $newFileName);



        $teachr = new Teachr();
        $teachr->setPrenom($data['prenom']);
        $teachr->setFormation($data['formation']);
        $teachr->setDescription($data['description']);
        $teachr->setPhoto($newFileName);


        $em->persist($teachr);
        $em->flush();

        $teachr = array(
            'id' => $teachr->getId(),
            'prenom' => $teachr->getPrenom(),
            'formation' => $teachr->getFormation(),
            'description' => $teachr->getDescription(),
            'photoURL' => $teachr->getPhoto(),
        );

        return new JsonResponse($teachr, 201);
    }

    // use this route to update a teachr!! the method it is set to POST because PUT does NOT support multipart/form-data every file upload with PUT will result in a error of file = null
    #[Route(path: '/updateteachr/{id}', name: 'updateTeachr', methods: ['POST'])]
    public function updateTeachr($id, TeachrRepository $teachrRepository, Request $request, ManagerRegistry $doctrine)
    {

        $em = $doctrine->getManager();
        $teachr = $teachrRepository->find($id);
        $data = json_decode($request->getContent(), true);

        //#[UploadedFile $UploadedFile]
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $request->files->get('photo');
        $destination = $this->getParameter('kernel.project_dir') . '/public/photos';
        $fileName = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $newFileName = $fileName . '-' . uniqid() . '.' . $uploadedFile->guessExtension();

        $uploadedFile->move($destination, $newFileName);

        $prenom = $request->request->get('prenom');
        $formation = $request->request->get('formation');
        $description = $request->request->get('description');



        $teachr->setPrenom($prenom);
        $teachr->setFormation($formation);
        $teachr->setDescription($description);
        $teachr->setPhoto('photos/' . $newFileName);

        $em->persist($teachr);
        $em->flush();

        $teachr = array(
            'id' => $teachr->getId(),
            'prenom' => $teachr->getPrenom(),
            'formation' => $teachr->getFormation(),
            'description' => $teachr->getDescription(),
            'photoURL' => $teachr->getPhoto(),
        );

        return new JsonResponse($teachr, 202);
    }
}
