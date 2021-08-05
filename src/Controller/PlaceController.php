<?php

namespace App\Controller;

use App\Entity\Place;
use App\Form\AddPlaceType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/place", name="place_")
 */
class PlaceController extends AbstractController
{
    /**
     * @Route("", name="main")
     */
    public function index(): Response
    {
        return $this->render('place/index.html.twig', [
            'controller_name' => 'PlaceController',
        ]);
    }

    /**
     * @Route("/add", name="add")
     */
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $place = new Place();
        $form = $this->createForm(AddPlaceType::class, $place);
        $form->handleRequest($request);
        $user = $this->getUser();

        if($form->isSubmitted() && $form->isValid() && $user->getIsActive()){

            $entityManager->persist($place);
            $entityManager->flush();

            $this->addFlash('success', 'Lieu crée !');
            return $this->redirectToRoute('main');
        }

        elseif (!$user->getIsActive()) {
            $this->addFlash('warning', 'Vous n\'avez pas l\'autorisation d\'ajouter un lieu.');
        }

        return $this->render('place/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}