<?php

namespace App\Controller;

use App\Entity\Teachr;
use App\Repository\TeachrRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
                'prenom' => $teachr->getPrenom()
            );
        }

        return new JsonResponse($teachrs, 200);
    }

    #[Route(path: '/newteachr', name: 'newtearch', methods: ['POST'])]
    public function newTeachr(Request $request, ManagerRegistry $doctrine)
    {


        $em = $doctrine->getManager();
        $data = json_decode($request->getContent(), true);


        $teachr = new Teachr();
        $teachr->setPrenom($data['prenom']);

        $em->persist($teachr);
        $em->flush();

        $teachr = array(
            'id' => $teachr->getId(),
            'prenom' => $teachr->getPrenom(),
        );

        return new JsonResponse($teachr, 201);
    }

    #[Route(path: '/updateteachr/{id}', name: 'updateTeachr', methods: ['PUT'])]
    public function updateTeachr($id, TeachrRepository $teachrRepository, Request $request, ManagerRegistry $doctrine)
    {

        $em = $doctrine->getManager();
        $teachr = $teachrRepository->find($id);
        $data = json_decode($request->getContent(), true);

        $teachr->setPrenom($data['prenom']);

        $em->persist($teachr);
        $em->flush();

        $teachr = array(
            'id' => $teachr->getId(),
            'prenom' => $teachr->getPrenom(),
        );

        return new JsonResponse($teachr, 202);
    }
}
