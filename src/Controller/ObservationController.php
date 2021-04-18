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
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

/**
 * @Route("/observation", name="observation.")
 */
class ObservationController extends AbstractAPIController
{
    /**
     * @Get("s/", name="list")
     * @QueryParam(name="orderby", requirements="(datetime|id)", default="datetime")
     * @QueryParam(name="sort", requirements="(asc|desc)", default="asc")
     * @QueryParam(name="start", default=null, nullable=true)
     * @QueryParam(name="end", default=null, nullable=true)
     * @QueryParam(name="day", strict=false, nullable=true, default=null, requirements="([1-9]|[12]\d|3[01])")
     * @QueryParam(name="month", strict=false, nullable=true, default=null, requirements="([1-9]|1[012])")
     * @QueryParam(name="year", strict=false, nullable=true, default=null, requirements="\d\d\d\d")
     * @QueryParam(name="results", strict=false, nullable=true, default=null, requirements="\d+")
     * @ParamConverter("start", isOptional="true", options={"format": "Y-m-d H:i:s"})
     * @ParamConverter("end", isOptional="true", options={"format": "Y-m-d H:i:s"})
     * @ViewAnnotation()
     */
    public function list(ParamFetcher $paramFetcher, ObservationRepository $observationRepository, ?\DateTime $start = null, ?\DateTime $end = null): View
    {
        $observations = $observationRepository->findAllWithCriteria(
            $paramFetcher->get('orderby'),
            $paramFetcher->get('sort'),
            $start,
            $end,
            $paramFetcher->get('day'),
            $paramFetcher->get('month'),
            $paramFetcher->get('year'),
            $paramFetcher->get('results')
        );

        return $this->defaultView($observations, Response::HTTP_OK);
    }

    /**
     * @Get("/{id}", name="find", requirements={"id"="\d+"})
     * @ViewAnnotation()
     */
    public function find(int $id, ObservationRepository $observationRepository): View
    {
        $observation = $observationRepository->find($id);
        return $this->defaultView($observation, Response::HTTP_OK);
    }

    /**
     * @Get("/", name="find_by_datetime")
     * @QueryParam(name="datetime", strict=true, nullable=false)
     * @ParamConverter("datetime", options={"format": "Y-m-d H:i:s"})
     * @ViewAnnotation()
     */
    public function findByDatetime(\DateTime $datetime, ObservationRepository $observationRepository): View
    {
        $observation = $observationRepository->findOneBy([
            'datetime' => $datetime
        ]);

        return $this->defaultView($observation, Response::HTTP_OK);
    }

    /**
     * @Post("/new", name="new")
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
    public function new(ParamFetcher $paramFetcher): View
    {
        $observation = new Observation();
        $form = $this->createForm(ObservationType::class, $observation);
        $form->submit($paramFetcher->all());

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($observation);
                $entityManager->flush();
            } catch (UniqueConstraintViolationException $exception) {
                return $this->defaultView(null, Response::HTTP_CONFLICT, null, true);
            }

            return $this->defaultView(null, Response::HTTP_CREATED, "Observation created with ID {$observation->getId()}.", true);
        } else
            return $this->defaultView(null, Response::HTTP_BAD_REQUEST, null, true);
    }

    /**
     * @Put("/{id}", name="update", requirements={"id"="\d+"})
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
            throw $this->createNotFoundException("No observation found for ID {$observation->getId()}");

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

        return $this->defaultView(null, Response::HTTP_OK, "Observation with ID {$observation->getId()} updated successfully.", true);
    }

    /**
     * @Delete("/{id}", name="delete", requirements={"id"="\d+"})
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

        return $this->defaultView(null, Response::HTTP_OK, "Observation with $id deleted successfully.", true);
    }

    /**
     * @Delete("/", name="delete_by_datetime")
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

        $id = $observation->getId();

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($observation);
        $entityManager->flush();

        return $this->defaultView(null, Response::HTTP_OK, "Observation with ID $id deleted successfully.", true);
    }
}
