<?php

namespace App\Controller;

use App\Entity\Journeys;
use App\Entity\Status;
use App\Form\CancelJourneyType;
use App\Form\EditJourneyType;
use App\Form\JourneyCreationType;
use App\Form\QuitJourneyType;
use App\Form\RegisterJourneyType;
use App\Repository\CityRepository;
use App\Repository\JourneysRepository;
use App\Repository\PlaceRepository;
use App\Repository\StatusRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Mobile_Detect;
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

        $now = new \DateTime();

        if($now > $journey->getDeadlineDate()){//check date
            $this->addFlash('danger', 'Date dépassé');
            return $this->redirectToRoute('main');
        }else{

            if ($journey->getNbInscriptionMax() <= $journey->getUsers()->count()){//check max users

                $this->addFlash('danger', 'Nombre maximum d\'insciption atteint');
                return $this->redirectToRoute('main');

            }else{


                $form = $this->createForm(RegisterJourneyType::class, $journey);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $user->getIsActive()) {

                    $journey->addUser($user);

                    $entityManager->persist($journey);
                    $entityManager->flush();


                    $this->addFlash('success', 'vous etes bien inscrit');
                    return $this->redirectToRoute('main');
                }
                elseif (!$user->getIsActive()) {
                    $this->addFlash('warning', 'Vous n\'avez pas l\'autorisation de vous inscrire.');
                }

                return $this->render('journey/register.html.twig', [
                    'journey' => $journey,
                    'form' => $form->createView(),
                ]);
            }


        }

    }


    /**
     * @Route("/{id}/quit", name="quit")
     */
    public function quit(int $id, JourneysRepository $journeysRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $journey = $journeysRepository->find($id);

        foreach($journey->getUsers() as $journey_user){
            if ($journey_user->getId() == $user->getId()){
                if ($journey->getStatus()->getName() == "Ouverte"){

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
                }else{
                    $this->addFlash('danger', 'Il erst trop tard pour se désister');
                    return $this->redirectToRoute('main');
                }
            }else{
                $this->addFlash('danger', 'Vous n\'êtes pas inscrit à  cette sortie ou êtes l\'oganisateur');
                return $this->redirectToRoute('main');
            }
        }

    }




    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, CityRepository $cityRepository, PlaceRepository $placeRepository, SerializerInterface $serializer, EntityManagerInterface $entityManager, StatusRepository $statusRepository): Response
    {

        $detect = new Mobile_Detect;
        $isMobile = $detect->isMobile();
        if ($isMobile){

            return $this->redirectToRoute('main');

        }else{

            $user = $this->getUser();
            $citysArray = $cityRepository->findAllArray();
            $citys = $cityRepository->findAll();
            $places = $placeRepository->findAllArray();


            $journey = new Journeys();
            $form = $this->createForm(JourneyCreationType::class, $journey);
            $form->handleRequest($request);


            //$jsonCitys = $serializer->serialize($citys, 'json');
            //$jsonPlaces = $serializer->serialize($places, 'json');



            if ($form->isSubmitted() && $form->isValid() && $user->getIsActive()) {

                $journey->setCollege($user->getCollege());
                $journey->setUser($user);

                //save
                if($form->get('save')->isClicked()){
                    $status = $statusRepository->findOneBy(array('name' => "Créée"));
                }else{//publish
                    $status = $statusRepository->findOneBy(array('name' => "Ouverte"));
                }

                $journey->setStatus($status);


                $entityManager->persist($journey);
                $entityManager->flush();

                $this->addFlash('success', 'Sortie créée !');
                return $this->redirectToRoute('main');
            }

            elseif (!$user->getIsActive()) {
                $this->addFlash('warning', 'Vous n\'avez pas l\'autorisation de créer une sortie');
            }


            return $this->render('journey/create.html.twig', [
                'form' => $form->createView(),
                'user' => $user,
                'citys' => $citys
            ]);

        }

    }


    /**
     * @Route("/{id}/edit", name="edit")
     */
    public function edit(int $id, Request $request, JourneysRepository $journeysRepository, StatusRepository $statusRepository, EntityManagerInterface $entityManager, CityRepository $cityRepository){

        $user = $this->getUser();
        $journey = $journeysRepository->find($id);
        $citys = $cityRepository->findAll();


        if ($user->getUsername() == $journey->getUser()->getUsername()){

            if ($journey->getStatus()->getName() == "Créée"){
                $form = $this->createForm(EditJourneyType::class, $journey);
                $form->handleRequest($request);

                if ($form->isSubmitted()){


                    //save
                    if($form->get('save')->isClicked() && $form->isValid()){

                        $status = $statusRepository->findOneBy(array('name' => "Créée"));
                        $journey->setStatus($status);
                        $entityManager->persist($journey);
                        $entityManager->flush();

                        $this->addFlash('success', 'Modifications enregistrées !');
                        return $this->redirectToRoute('main');

                    }elseif($form->get('publish')->isClicked() && $form->isValid()){//publish

                        $status = $statusRepository->findOneBy(array('name' => "Ouverte"));
                        $journey->setStatus($status);
                        $entityManager->persist($journey);
                        $entityManager->flush();

                        $this->addFlash('success', 'Modifications enregistrées et Sortie publié !');
                        return $this->redirectToRoute('main');

                    }elseif($form->get('delete')->isClicked()){//delete

                        $entityManager->remove($journey);
                        $entityManager->flush();

                        $this->addFlash('danger', 'Sortie supprimé !');
                        return $this->redirectToRoute('main');
                    }

                }

                return $this->render('journey/edit.html.twig', [
                    'form' => $form->createView(),
                    'user' => $user,
                    'journey' => $journey,
                    'citys' => $citys,
                ]);

            }else{
                $this->addFlash('danger', 'Vous ne pouvez pas modifier cette sortie');
                return $this->redirectToRoute('main');
            }

        }else{
            $this->addFlash('danger', 'Vous n\'êtes pas le créateur de cette sortie');
            return $this->redirectToRoute('main');
        }

    }



    /**
     * @Route("/{id}/cancel", name="cancel")
     */
    public function cancel(int $id, Request $request, JourneysRepository $journeysRepository, StatusRepository $statusRepository, EntityManagerInterface $entityManager){

        $user = $this->getUser();
        $journey = $journeysRepository->find($id);


        if ($user->getUsername() == $journey->getUser()->getUsername()){

            if ($journey->getStatus()->getName() == "Créée" || $journey->getStatus()->getName() == "Ouverte"){
                $journey->setDescription('');

                $form = $this->createForm(CancelJourneyType::class, $journey);
                $form->handleRequest($request);


                if ($form->isSubmitted() && $form->isValid()){
                    $status = $statusRepository->findOneBy(array('name' => "Annulée"));
                    $journey->setStatus($status);
                    $entityManager->persist($journey);
                    $entityManager->flush();

                }

                return $this->render('journey/cancel.html.twig', [
                    'journey' => $journey,
                    'form' => $form->createView()
                ]);
            }else{
                $this->addFlash('danger', 'Vous ne pouvez pas annuler cette sortie');
                return $this->redirectToRoute('main');
            }

        }else{
            $this->addFlash('danger', 'Vous n\'êtes pas le créateur de cette sortie');
            return $this->redirectToRoute('main');
        }





    }



}