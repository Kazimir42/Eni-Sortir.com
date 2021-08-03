<?php

namespace App\Controller;

use App\Entity\City;
use App\Form\AddCityType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/city", name="city_")
 */
class CityController extends AbstractController
{
    /**
     * @Route("", name="main")
     */
    public function index(): Response
    {
        return $this->render('city/index.html.twig', [
            'controller_name' => 'CityController',
        ]);
    }

    /**
     * @Route("/add", name="add")
     */
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $city = new City();
        $form = $this->createForm(AddCityType::class, $city);
        $form->handleRequest($request);
        $user = $this->getUser();


        if($form->isSubmitted() && $form->isValid() && $user->getIsActive()){

            $entityManager->persist($city);
            $entityManager->flush();

            $this->addFlash('success', 'Ville créée !');
            return $this->redirectToRoute('main');
        }

        elseif (!$user->getIsActive()) {
            $this->addFlash('warning', 'Vous n\'avez pas l\'autorisation d\'ajouter une ville.');
        }

        return $this->render('city/add.html.twig', [
            'form' => $form->createView()
        ]);
    }
}