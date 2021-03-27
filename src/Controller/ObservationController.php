<?php

namespace App\Controller;

use App\Entity\Observation;
use App\Form\ObservationType;
use App\Repository\ObservationRepository;
use App\Controller\AbstractAPIController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\View as ViewAnnotation;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/observation", name="observation.")
 */
class ObservationController extends AbstractAPIController
{
    /**
     * @Get("/list", name="list")
     * @QueryParam(name="orderby", requirements="(datetime|id)", default="datetime")
     * @QueryParam(name="sort", requirements="(asc|desc)", default="asc")
     * @QueryParam(name="start", default=null, nullable=true)
     * @QueryParam(name="end", default=null, nullable=true)
     * @ParamConverter("start", isOptional="true", options={"format": "Y-m-d H:i:s"})
     * @ParamConverter("end", isOptional="true", options={"format": "Y-m-d H:i:s"})
     * @ViewAnnotation()
     */
    public function list(ParamFetcher $paramFetcher, ObservationRepository $observationRepository, ?\DateTime $start = null, ?\DateTime $end = null): View
    {
        $observationList = $observationRepository->findAllWithCriteria(
            $paramFetcher->get('orderby'),
            $paramFetcher->get('sort'),
            $start,
            $end
        );

        return $this->defaultView($observationList, Response::HTTP_OK, null, 'No observation found.');
    }

    /**
     * @Get("/find", name="find_by_datetime")
     * @QueryParam(name="datetime", strict=true, nullable=true, default=null)
     * @ParamConverter("datetime", options={"format": "Y-m-d H:i:s"})
     * @ViewAnnotation()
     */
    public function findByDatetime(\DateTime $datetime, ObservationRepository $observationRepository): View
    {
        $observation = $observationRepository->findOneBy([
            'datetime' => $datetime
        ]);

        return $this->defaultView($observation, Response::HTTP_OK, null, "No observation found for datetime {$datetime->format('Y-m-d H:i:s')}.");
    }

    /**
     * @Get("/{id}", name="find")
     * @ViewAnnotation()
     */
    public function find(int $id, ObservationRepository $observationRepository): View
    {
        $observation = $observationRepository->find($id);
        return $this->defaultView($observation, Response::HTTP_OK, null, "No observation found for id {$id}.");
    }

    /**
     * @Delete("/delete", name="delete_by_datetime")
     * @QueryParam(name="datetime", strict=true, nullable=false)
     * @ParamConverter("datetime", options={"format": "Y-m-d H:i:s"})
     * @ViewAnnotation()
     */
    public function deleteByDatetime(\DateTime $datetime, ObservationRepository $observationRepository)
    {
        $observation = $observationRepository->findOneBy([
            'datetime' => $datetime
        ]);

        if (!$observation)
            throw $this->createNotFoundException("No observation found for datetime {$datetime->format('Y-m-d H:i:s')}.");

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($observation);
        $entityManager->flush();

        return $this->defaultView(null, Response::HTTP_OK, 'Observation deleted successfully.', null, true);
    }

    /**
     * @Delete("/{id}", name="delete")
     * @ViewAnnotation()
     */
    public function delete(int $id, ObservationRepository $observationRepository): View
    {
        $observation = $observationRepository->find($id);

        if (!$observation)
            throw $this->createNotFoundException("No observation found for id $id.");

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($observation);
        $entityManager->flush();

        return $this->defaultView(null, Response::HTTP_OK, 'Observation deleted successfully.', null, true);
    }

    /**
     * @Post("/create", name="create")
     * @RequestParam(name="datetime", strict=true, nullable=false)
     * @ParamConverter("datetime", options={"format": "Y-m-d H:i:s"})
     * @RequestParam(name="aTemp", requirements="[+-]?([0-9]*[.])?[0-9]+", nullable=true, default=null)
     * @RequestParam(name="aHum", requirements="\d+", nullable=true, default=null)
     * @RequestParam(name="bTemp", requirements="[+-]?([0-9]*[.])?[0-9]+", nullable=true, default=null)
     * @RequestParam(name="bHum", requirements="\d+", nullable=true, default=null)
     * @RequestParam(name="extTemp", requirements="[+-]?([0-9]*[.])?[0-9]+", nullable=true, default=null)
     * @RequestParam(name="extHum", requirements="\d+", nullable=true, default=null)
     * @ViewAnnotation()
     */
    public function create(ParamFetcher $paramFetcher): View
    {
        $observation = new Observation();
        $form = $this->createForm(ObservationType::class, $observation);
        $form->submit($paramFetcher->all());

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($observation);
            $entityManager->flush();

            return $this->defaultView(null, Response::HTTP_CREATED, 'Observation created successfully.', null, true);
        } else
            return $this->defaultView(null, Response::HTTP_BAD_REQUEST, null, null, true);
    }

    /**
     * @Put("/{id}", name="update")
     * @RequestParam(name="aTemp", requirements="[+-]?([0-9]*[.])?[0-9]+", nullable=true, default=null)
     * @RequestParam(name="aHum", requirements="\d+", nullable=true, default=null)
     * @RequestParam(name="bTemp", requirements="[+-]?([0-9]*[.])?[0-9]+", nullable=true, default=null)
     * @RequestParam(name="bHum", requirements="\d+", nullable=true, default=null)
     * @RequestParam(name="extTemp", requirements="[+-]?([0-9]*[.])?[0-9]+", nullable=true, default=null)
     * @RequestParam(name="extHum", requirements="\d+", nullable=true, default=null)
     * @ViewAnnotation()
     */
    public function update(Observation $observation, ParamFetcher $paramFetcher)
    {
        if (!$observation)
            throw $this->createNotFoundException("No observation found for id {$observation->getId()}");

        if (!is_null($paramFetcher->get('aTemp')))
            $observation->setATemp($paramFetcher->get('aTemp'));

        if (!is_null($paramFetcher->get('aHum')))
            $observation->setAHum($paramFetcher->get('aHum'));

        if (!is_null($paramFetcher->get('bTemp')))
            $observation->setBTemp($paramFetcher->get('bTemp'));

        if (!is_null($paramFetcher->get('bHum')))
            $observation->setBHum($paramFetcher->get('bHum'));

        if (!is_null($paramFetcher->get('extTemp')))
            $observation->setExtTemp($paramFetcher->get('extTemp'));

        if (!is_null($paramFetcher->get('extHum')))
            $observation->setExtHum($paramFetcher->get('extHum'));

        $this->getDoctrine()->getManager()->flush();

        return $this->defaultView(null, Response::HTTP_OK, 'Observation updated successfully.', null, true);
    }
}
