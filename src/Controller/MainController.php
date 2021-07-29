<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Form\FilterFormType;
use App\Repository\CollegeRepository;
use App\Repository\JourneysRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("", name="main")
     */
    public function index(JourneysRepository $journeysRepository, CollegeRepository $collegeRepository, Request $request): Response
    {

        $user = $this->getUser();

        $data = new SearchData();
        $data->toSearch = $request->get('toSearch', '');
        $form = $this->createForm(FilterFormType::class, $data);
        $form->handleRequest($request);



        $journeys = $journeysRepository->findSearch($data);




        return $this->render('main/index.html.twig', [
            'journeys' => $journeys,
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
}
