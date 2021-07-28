<?php

namespace App\Controller;

use App\Entity\Journeys;
use App\Entity\Status;
use App\Form\JourneyCreationType;
use App\Form\QuitJourneyType;
use App\Form\RegisterJourneyType;
use App\Repository\CityRepository;
use App\Repository\JourneysRepository;
use App\Repository\PlaceRepository;
use App\Repository\StatusRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * @Route("/journey", name="journey_")
 */
class JourneyController extends AbstractController
{
    /**
     * @Route("", name="home")
     */
    public function index(): Response
    {
        return $this->render('journey/index.html.twig', [
            'controller_name' => 'JourneyController',
        ]);
    }

    /**
     * @Route("/{id}/view", name="view")
     */
    public function view(int $id, JourneysRepository $journeysRepository): Response
    {
        $journey = $journeysRepository->find($id);


        return $this->render('journey/view.html.twig', [
            'journey' => $journey,
        ]);
    }

    /**
     * @Route("/{id}/register", name="register")
     */
    public function register(int $id, JourneysRepository $journeysRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $journey = $journeysRepository->find($id);

        $form = $this->createForm(RegisterJourneyType::class, $journey);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {

            $journey->addUser($user);

            $entityManager->persist($journey);
            $entityManager->flush();


            $this->addFlash('success', 'vous etes bien inscrit');
            return $this->redirectToRoute('main');
        }



        return $this->render('journey/register.html.twig', [
            'journey' => $journey,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/quit", name="quit")
     */
    public function quit(int $id, JourneysRepository $journeysRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $journey = $journeysRepository->find($id);

        $form = $this->createForm(QuitJourneyType::class, $journey);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {

            $journey->removeUser($user);

            $entityManager->persist($journey);
            $entityManager->flush();

            $this->addFlash('success', 'vous vous êtes bien désisté');
            return $this->redirectToRoute('main');
        }



        return $this->render('journey/quit.html.twig', [
            'journey' => $journey,
            'form' => $form->createView(),
        ]);
    }




    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, CityRepository $cityRepository, PlaceRepository $placeRepository, SerializerInterface $serializer, EntityManagerInterface $entityManager, StatusRepository $statusRepository): Response
    {
        $user = $this->getUser();
        $citys = $cityRepository->findAllArray();
        $places = $placeRepository->findAllArray();


        $journey = new Journeys();
        $form = $this->createForm(JourneyCreationType::class, $journey);
        $form->handleRequest($request);


        $jsonCitys = $serializer->serialize($citys, 'json');
        $jsonPlaces = $serializer->serialize($places, 'json');



        if ($form->isSubmitted() && $form->isValid()) {

            $journey->setCollege($user->getCollege());
            $journey->setUser($user);


            $status = $statusRepository->findOneBy(array('name' => "Ouverte"));

            $journey->setStatus($status);


            $entityManager->persist($journey);
            $entityManager->flush();

            $this->addFlash('success', 'Sortie crée !');
            return $this->redirectToRoute('main');
        }


        return $this->render('journey/create.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'citys' => $jsonCitys,
            'placesJson' => $jsonPlaces,
            'places' => $places,
        ]);
    }
}
