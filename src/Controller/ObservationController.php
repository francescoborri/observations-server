<?php

namespace App\Controller;

use App\Entity\Observation;
use App\Form\ObservationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/observation", name="observation.")
 */
class ObservationController extends AbstractController
{
    /**
     * @Route("/list", name="list", methods={"GET"})
     */
    public function list(): Response
    {
        $data = [
            'data' => $this->getDoctrine()->getRepository(Observation::class)->findAll()
        ];
        return $this->json($data);
    }

    /**
     * @Route("/{datetime}", name="show", methods={"GET"})
     */
    public function show(Request $request): Response
    {
        $datetime = $request->attributes->get('datetime');
        $observation = $this->getDoctrine()->getRepository(Observation::class)->find($datetime);
        $response = null;
        $data = [];

        if (is_null($observation)) {
            $data['data'] = [];
            $response = $this->json($data, 404);
        } else {
            $data['data'] = $observation;
            $response = $this->json($data);
        }

        return $response;
    }

    /**
     * @Route("/create", name="create", methods={"POST"})
     */
    public function create(Request $request): Response
    {
        $observation = new Observation();
        $form = $this->createForm(ObservationType::class, $observation);
        $form->submit(json_decode($request->getContent(), true));

        $response = null;
        $data = [];

        // dump(json_decode($request->getContent(), true));
        // dump($observation);

        if ($form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($observation);
            $entityManager->flush();

            $data['data'] = 'Observation submitted successfully.';
            $response = $this->json($data, 201);
        } else {
            $data['data'] = 'Observation was not submitted.';
            $response = $this->json($data, 400);
        }

        return $response;
    }
}
