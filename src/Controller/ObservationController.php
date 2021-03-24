<?php

namespace App\Controller;

use App\Entity\Observation;
use App\Form\ObservationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

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
        $form = $this->createFormBuilder(null, [
            'csrf_protection' => false
        ])
            ->add('datetime', DateTimeType::class, [
                'html5' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd HH:mm:ss',
                'constraints' => new NotNull()
            ])
            ->getForm();
        
        $form->submit($request->attributes->get('_route_params'));

        $response = null;
        $data = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $observation = $this->getDoctrine()->getRepository(Observation::class)->findBy([
                'datetime' => $form->getData()['datetime']
            ]);

            if (empty($observation)) {
                $data['data'] = [];
                $response = $this->json($data, 404);
            } else {
                $data['data'] = $observation;
                $response = $this->json($data);
            }
        } else {
            $data['data'] = 'Malformed request.';
            $response = $this->json($data, 400);
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

        if ($form->isSubmitted() && $form->isValid()) {
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
