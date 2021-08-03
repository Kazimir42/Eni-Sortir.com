<?php

namespace App\Controller;

use App\Repository\PlaceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CallDbController extends AbstractController
{
    /**
     * @Route("/call/db/places", name="call_db_places")
     */
    public function index(PlaceRepository $placeRepository, Request $request): Response
    {
        $cityId = $request->query->get('cityId');


        $places = $placeRepository->findAllByIdArray($cityId);


        return new JsonResponse($places);
    }

    /**
     * @Route("/call/db/info", name="call_db_info")
     */
    public function info(PlaceRepository $placeRepository, Request $request): Response
    {
        $placeId = $request->query->get('placeId');


        $place = $placeRepository->findOneById($placeId);

        $placeObject = $placeRepository->find($placeId);

        $place[0]['postal'] = $placeObject->getCity()->getPostal();

        return new JsonResponse($place);
    }
}
