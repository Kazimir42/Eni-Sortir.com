<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Form\FilterFormType;
use App\Repository\CollegeRepository;
use App\Repository\JourneysRepository;
use App\Service\UpdateJourneys;
use Mobile_Detect;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("", name="main")
     */
    public function index(JourneysRepository $journeysRepository, CollegeRepository $collegeRepository, Request $request, UpdateJourneys $updateJourneys): Response
    {
        $detect = new Mobile_Detect;
        $isMobile = $detect->isMobile();

        //CHANGE TO COMMAND WHEN DATETIME STOP WTF BUGGING
        $updateJourneys->updateStatusJourney();

        $user = $this->getUser();

        $data = new SearchData();
        $data->toSearch = $request->get('toSearch', '');
        $form = $this->createForm(FilterFormType::class, $data);
        $form->handleRequest($request);

        //OPTIMIZE THIS PLZ LOTS OF REQUEST -_-
        $journeys = $journeysRepository->findSearch($data);

        //$time = new \DateTime();
        //$result = $time->format('Y-m-d H:i:s');
        //dd($result);




        return $this->render('main/index.html.twig', [
            'journeys' => $journeys,
            'form' => $form->createView(),
            'user' => $user,
            'mobile' => $isMobile,
        ]);
    }
}
