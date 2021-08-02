<?php

namespace App\Controller;

use App\Entity\College;
use App\Entity\Journeys;
use App\Entity\User;
use App\Form\AddCollegeType;
use App\Form\AddJourneyAdminType;
use App\Form\DeleteJourneyAdminType;
use App\Form\DeletePlaceType;
use App\Form\EditCityType;
use App\Form\EditCollegeType;
use App\Form\EditJourneyAdminType;
use App\Form\EditPlaceType;
use App\Repository\CityRepository;
use App\Repository\CollegeRepository;
use App\Repository\JourneysRepository;
use App\Repository\PlaceRepository;
use App\Repository\StatusRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////
    /// USER
    ////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * @Route("/admin/users", name="admin_users")
     */
    public function users(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('admin/users.html.twig', [
            'controller_name' => 'AdminController',
            'users' => $users
        ]);
    }

    /**
     * @Route("/admin/disable/{username}", name="admin_disable")
     */
    public function disable(User $user, EntityManagerInterface $entityManager): Response
    {
        $user->setIsActive(false);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('admin_users');

    }

    /**
     * @Route("/admin/enable/{username}", name="admin_enable")
     */
    public function enable(User $user, EntityManagerInterface $entityManager): Response
    {
        $user->setIsActive(true);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('admin_users');
    }

    /**
     * @Route("/admin/delete/{username}", name="admin_delete")
     */
    public function delete(User $user, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('admin_users');
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////
    /// CITY
    ////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * @Route("/admin/city", name="admin_city")
     */
    public function city(CityRepository $cityRepository): Response
    {
        $citys = $cityRepository->findAll();

        return $this->render('admin/city.html.twig', [
            'citys' => $citys
        ]);
    }

    /**
     * @Route("/admin/city/{id}/edit", name="admin_city_edit")
     */
    public function cityEdit(int $id, CityRepository $cityRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $city = $cityRepository->find($id);

        $form = $this->createForm(EditCityType::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($city);
            $entityManager->flush();

            $this->addFlash('success', 'Ville modifié !');
            return $this->redirectToRoute('admin');
        }

        return $this->render('admin/city_edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/city/{id}/delete", name="admin_city_delete")
     */
    public function cityDelete(int $id, CityRepository $cityRepository): Response
    {
        $city = $cityRepository->find($id);

        return $this->render('admin/city_delete.html.twig', [
            'city' => $city
        ]);
    }


    ////////////////////////////////////////////////////////////////////////////////////////////////
    /// PLACE
    ////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * @Route("/admin/place", name="admin_place")
     */
    public function place(PlaceRepository $placeRepository): Response
    {
        $places = $placeRepository->findAll();

        return $this->render('admin/place.html.twig', [
            'places' => $places
        ]);
    }

    /**
     * @Route("/admin/place/{id}/edit", name="admin_place_edit")
     */
    public function placeEdit(int $id, PlaceRepository $placeRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $place = $placeRepository->find($id);

        $form = $this->createForm(EditPlaceType::class, $place);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($place);
            $entityManager->flush();

            $this->addFlash('success', 'Lieu modifié !');
            return $this->redirectToRoute('admin');
        }

        return $this->render('admin/place_edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/place/{id}/delete", name="admin_place_delete")
     */
    public function placeDelete(int $id, PlaceRepository $placeRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        $place = $placeRepository->find($id);

        $form = $this->createForm(DeletePlaceType::class, $place);
        $form->handleRequest($request);

        if ($form->isSubmitted()){

            dd('supression du lieu');

            $this->addFlash('success', 'Lieu supprimé !');
            return $this->redirectToRoute('admin');
        }

        return $this->render('admin/place_delete.html.twig', [
            'place' => $place,
            'form' => $form->createView()
        ]);
    }




    ////////////////////////////////////////////////////////////////////////////////////////////////
    /// COLLEGE
    ////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * @Route("/admin/college", name="admin_college")
     */
    public function college(CollegeRepository $collegeRepository): Response
    {
        $colleges = $collegeRepository->findAll();

        return $this->render('admin/college.html.twig', [
            'colleges' => $colleges
        ]);
    }


    /**
     * @Route("/admin/college/add", name="admin_college_add")
     */
    public function collegeAdd(Request $request, EntityManagerInterface $entityManager): Response
    {
        $college = new College();
        $form = $this->createForm(AddCollegeType::class, $college);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()){

            $entityManager->persist($college);
            $entityManager->flush();

            $this->addFlash('success', 'Campus créée !');
            return $this->redirectToRoute('main');
        }


        return $this->render('admin/college_add.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/admin/college/{id}/edit", name="admin_college_edit")
     */
    public function collegeEdit(int $id, CollegeRepository $collegeRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $colleges = $collegeRepository->find($id);

        $form = $this->createForm(EditCollegeType::class, $colleges);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($colleges);
            $entityManager->flush();

            $this->addFlash('success', 'Campus modifié !');
            return $this->redirectToRoute('admin');
        }

        return $this->render('admin/college_edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/college/{id}/delete", name="admin_college_delete")
     */
    public function collegeDelete(int $id, PlaceRepository $placeRepository): Response
    {
        $colleges = $placeRepository->find($id);

        return $this->render('admin/college_delete.html.twig', [
            'colleges' => $colleges
        ]);
    }


    ////////////////////////////////////////////////////////////////////////////////////////////////
    /// JOURNEY
    ////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * @Route("/admin/journey", name="admin_journey")
     */
    public function journey(JourneysRepository $journeysRepository): Response
    {
        $journey = $journeysRepository->findAll();

        return $this->render('admin/journey.html.twig', [
            'journeys' => $journey
        ]);
    }

    /**
     * @Route("/admin/journey/add", name="admin_journey_add")
     */
    public function add(Request $request, EntityManagerInterface $entityManager, StatusRepository $statusRepository): Response
    {
        $journey = new Journeys();
        $form = $this->createForm(AddJourneyAdminType::class, $journey);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()){

            $journey->setCollege($journey->getUser()->getCollege());

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


        return $this->render('admin/journey_add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/journey/{id}/edit", name="admin_journey_edit")
     */
    public function journeyEdit(int $id, JourneysRepository $journeysRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $journey = $journeysRepository->find($id);

        $form = $this->createForm(EditJourneyAdminType::class, $journey);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($journey);
            $entityManager->flush();

            $this->addFlash('success', 'Sortie modifié !');
            return $this->redirectToRoute('admin');
        }

        return $this->render('admin/journey_edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/journey/{id}/delete", name="admin_journey_delete")
     */
    public function journeyDelete(int $id, JourneysRepository $journeysRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        $journey = $journeysRepository->find($id);

        $form = $this->createForm(DeleteJourneyAdminType::class, $journey);
        $form->handleRequest($request);

        if ($form->isSubmitted()){

            dd('supression de la sortie');

            $this->addFlash('success', 'Sortie supprimé !');
            return $this->redirectToRoute('admin');
        }

        return $this->render('admin/journey_delete.html.twig', [
            'journey' => $journey,
            'form' => $form->createView()
        ]);
    }
}