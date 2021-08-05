<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Entity\College;
use App\Entity\Journeys;
use App\Entity\User;
use App\Form\AddCollegeType;
use App\Form\AddJourneyAdminType;
use App\Form\CancelJourneyType;
use App\Form\DeleteCityType;
use App\Form\DeleteJourneyAdminType;
use App\Form\DeletePlaceType;
use App\Form\EditCityType;
use App\Form\EditCollegeType;
use App\Form\EditJourneyAdminType;
use App\Form\EditPlaceType;
use App\Form\SearchFormType;
use App\Form\UploadCSVType;
use App\Repository\CityRepository;
use App\Repository\CollegeRepository;
use App\Repository\JourneysRepository;
use App\Repository\PlaceRepository;
use App\Repository\StatusRepository;
use App\Repository\UserRepository;
use App\Service\UpdateJourneys;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\String\Slugger\SluggerInterface;

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
    public function delete(User $user, EntityManagerInterface $entityManager, UpdateJourneys $updateJourneys): Response
    {
        $updateJourneys->updateUserJourney($user);

        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('admin_users');
    }

    /**
     * @Route("admin/add_csv", name="admin_add_csv")
     */
    public function addCSV(Request $request, UserPasswordHasherInterface $passwordEncoder, SluggerInterface $slugger, CollegeRepository $collegeRepository, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm( UploadCSVType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form->get('file')->getData();

            if ($file) {

                $fileExtension = 'csv';

                $normalizers = [new ObjectNormalizer()];

                $encoders = [
                    new CsvEncoder()
                ];

                $serializer = new Serializer($normalizers, $encoders);

                $fileString = file_get_contents($file);

                $data = $serializer->decode($fileString, $fileExtension);

                foreach ($data as $item){


                    try {

                        $theCollege = $collegeRepository->findOneBy(array("id" => $item["college_id"]));

                        if ($item["roles"] == "admin") {
                            $roleArray = ["ROLE_ADMIN"];
                        } else {
                            $roleArray = [];
                        }

                        $user = new User();

                        $user->setCollege($theCollege);
                        $user->setUsername($item["username"]);
                        $user->setRoles($roleArray);
                        $user->setPassword(
                            $passwordEncoder->hashPassword(
                                $user,
                                $item["password"]
                            ));
                        $user->setLastname($item["lastname"]);
                        $user->setFirstname($item["firstname"]);
                        $user->setPhone($item["phone"]);
                        $user->setMail($item["mail"]);
                        $user->setIsActive($item["is_active"]);
                        $user->setAvatar($item["avatar"]);

                        $entityManager->persist($user);
                        $entityManager->flush();
                    }catch (\Exception $e){
                        $this->addFlash('danger', 'Erreur lors de l\'import des utilisateurs');
                        return $this->redirectToRoute('admin');
                    }

                }


            }else{
                $this->addFlash('danger', 'Erreur lors de l\'ajout du fichier');
                return $this->redirectToRoute('admin');
            }

            $this->addFlash('success', 'Utilisateurs ajoutés !');
            return $this->redirectToRoute('admin');
        }

        return $this->render('admin/add_csv.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////
    /// CITY
    ////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * @Route("/admin/city", name="admin_city")
     */
    public function city(CityRepository $cityRepository, Request $request): Response
    {
        $data = new SearchData();
        $data->toSearch = $request->get('toSearch', '');
        $form = $this->createForm(SearchFormType::class, $data);
        $form->handleRequest($request);

        $citys = $cityRepository->findSearch($data->toSearch);

        return $this->render('admin/city.html.twig', [
            'citys' => $citys,
            'form' => $form->createView()
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
    public function cityDelete(int $id, CityRepository $cityRepository, Request $request, StatusRepository $statusRepository, EntityManagerInterface $entityManager, PlaceRepository $placeRepository): Response
    {
        $city = $cityRepository->find($id);

        $canceledStatus = $statusRepository->findOneBy(array("name" => "Annulée"));
        $canceledPlace = $placeRepository->findOneBy(array("name" => "Inconnue"));
        $canceledCity = $cityRepository->findOneBy(array("name" => "Inconnue"));

        $form = $this->createForm(DeleteCityType::class, $city);
        $form->handleRequest($request);

        if($form->isSubmitted()){

            $places = $city->getPlace();

            foreach ($places as $place){

                $journeys = $place->getJourney();

                foreach ($journeys as $journey){
                    //journey on curse or ended so dont cancel
                    if ($journey->getStatus()->getName() == "Activité en cours" || $journey->getStatus()->getName() == "Passée" ||  $journey->getStatus()->getName() == "Annulée"){
                        $journey->setPlace($canceledPlace);
                        $entityManager->persist($journey);
                        $entityManager->flush();

                    }else{//cancel other
                        $journey->setStatus($canceledStatus);
                        $journey->setPlace($canceledPlace);
                        $entityManager->persist($journey);
                        $entityManager->flush();
                    }
                }
                $entityManager->remove($place);
                $entityManager->flush();
            }

            $entityManager->remove($city);
            $entityManager->flush();

            $this->addFlash('success', 'Ville supprimé !');
            return $this->redirectToRoute('admin');
        }

        return $this->render('admin/city_delete.html.twig', [
            'city' => $city,
            'form' => $form->createView(),
        ]);
    }


    ////////////////////////////////////////////////////////////////////////////////////////////////
    /// PLACE
    ////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * @Route("/admin/place", name="admin_place")
     */
    public function place(PlaceRepository $placeRepository, Request $request): Response
    {
        $data = new SearchData();
        $data->toSearch = $request->get('toSearch', '');
        $form = $this->createForm(SearchFormType::class, $data);
        $form->handleRequest($request);

        $places = $placeRepository->findSearch($data->toSearch);

        return $this->render('admin/place.html.twig', [
            'places' => $places,
            'form' => $form->createView()
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
    public function placeDelete(int $id, PlaceRepository $placeRepository, EntityManagerInterface $entityManager, Request $request, StatusRepository $statusRepository): Response
    {
        $place = $placeRepository->find($id);
        $canceledPlace = $placeRepository->findOneBy(array("name" => "Inconnue"));
        $canceledStatus = $statusRepository->findOneBy(array("name" => "Annulée"));


        $form = $this->createForm(DeletePlaceType::class, $place);
        $form->handleRequest($request);

        if ($form->isSubmitted()){

            $journeys = $place->getJourney();

            foreach ($journeys as $journey){
                if ($journey->getStatus()->getName() == "Activité en cours" || $journey->getStatus()->getName() == "Passée" ||  $journey->getStatus()->getName() == "Annulée"){
                    $journey->setPlace($canceledPlace);
                    $entityManager->persist($journey);
                    $entityManager->flush();

                }else{//cancel other
                    $journey->setStatus($canceledStatus);
                    $journey->setPlace($canceledPlace);
                    $entityManager->persist($journey);
                    $entityManager->flush();
                }
            }

            $entityManager->remove($place);
            $entityManager->flush();

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
    public function college(CollegeRepository $collegeRepository, Request $request): Response
    {
        $data = new SearchData();
        $data->toSearch = $request->get('toSearch', '');
        $form = $this->createForm(SearchFormType::class, $data);
        $form->handleRequest($request);

        $colleges = $collegeRepository->findSearch($data->toSearch);

        return $this->render('admin/college.html.twig', [
            'colleges' => $colleges,
            'form' => $form->createView()
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
    public function journey(JourneysRepository $journeysRepository, Request $request): Response
    {
        $data = new SearchData();
        $data->toSearch = $request->get('toSearch', '');
        $form = $this->createForm(SearchFormType::class, $data);
        $form->handleRequest($request);

        $journey = $journeysRepository->findSearchMini($data->toSearch);


        return $this->render('admin/journey.html.twig', [
            'journeys' => $journey,
            'form' => $form->createView()
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
     * @Route("/admin/journey/{id}/cancel", name="admin_journey_cancel")
     */
    public function journeyCancel(int $id, JourneysRepository $journeysRepository, Request $request, EntityManagerInterface $entityManager, StatusRepository $statusRepository): Response
    {
        $journey = $journeysRepository->find($id);
        $journey->setDescription('');

        $form = $this->createForm(CancelJourneyType::class, $journey);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $status = $statusRepository->findOneBy(array('name' => "Annulée"));
            $journey->setStatus($status);

            $entityManager->persist($journey);
            $entityManager->flush();

            $this->addFlash('success', 'Sortie Annulée !');
            return $this->redirectToRoute('admin');
        }

        return $this->render('admin/journey_cancel.html.twig', [
            'journey' => $journey,
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

            $entityManager->remove($journey);
            $entityManager->flush();

            $this->addFlash('success', 'Sortie supprimé !');
            return $this->redirectToRoute('admin');
        }

        return $this->render('admin/journey_delete.html.twig', [
            'journey' => $journey,
            'form' => $form->createView()
        ]);
    }
}