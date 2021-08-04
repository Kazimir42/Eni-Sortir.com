<?php

namespace App\Controller;

use App\Entity\Groupe;
use App\Form\AddMemberGroupeType;
use App\Form\CreateGroupeType;
use App\Form\KickMemberGroupeType;
use App\Repository\GroupeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/groupe", name="groupe_")
 */
class GroupeController extends AbstractController
{
    /**
     * @Route("", name="home")
     */
    public function index(GroupeRepository $groupeRepository): Response
    {
        $user = $this->getUser();

        $groupesOwner = $groupeRepository->findBy(array('owner' => $user));

        return $this->render('groupe/index.html.twig', [
            'user' => $user,
            'groupesOwner' => $groupesOwner
        ]);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(GroupeRepository $groupeRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $groupe = new Groupe();

        $form = $this->createForm(CreateGroupeType::class, $groupe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $groupe->setOwner($user);

            $entityManager->persist($groupe);
            $entityManager->flush();

            $this->addFlash('success', 'Groupe créée !');
            return $this->redirectToRoute('groupe_home');

        }


        return $this->render('groupe/create.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}", name="view")
     */
    public function view(int $id, GroupeRepository $groupeRepository): Response
    {
        $groupe = $groupeRepository->find($id);

        $members = $groupe->getMembers();
        $owner = $groupe->getOwner();

        $user = $this->getUser();

        //$groupesOwner = $groupeRepository->findBy(array('owner' => $user));

        return $this->render('groupe/view.html.twig', [
            'user' => $user,
            'groupe' => $groupe,
            'users' => $members,
            'owner' => $owner,
        ]);
    }

    /**
     * @Route("/{id}/add", name="add")
     */
    public function add(int $id, GroupeRepository $groupeRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $groupe = $groupeRepository->find($id);

        $form = $this->createForm(AddMemberGroupeType::class, $groupe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            try {
            $userToAdd = $form->get('user')->getData();


            foreach ($groupe->getMembers() as $member){
                if($member->getUsername() == $userToAdd->getUsername()){
                    //le user est deja member
                    throw new \Exception('user already in');
                }
            }
            if ($groupe->getOwner()->getUsername() == $userToAdd->getUsername()){
                //le user est deja owner
                throw new \Exception('user already owner');
            }

            $groupe->addMember($userToAdd);
            $entityManager->persist($groupe);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur ajouté !');
            return $this->redirectToRoute('groupe_view', ['id' => $id]);

            }catch (\Exception $e){
                $this->addFlash('danger', 'Erreur lors de l\'ajout!');
                return $this->redirectToRoute('groupe_view', ['id' => $id]);
            }

        }


        return $this->render('groupe/add.html.twig', [
            'groupe' => $groupe,
            'form' => $form->createView()
        ]);
    }



    /**
     * @Route("/{id}/kick/{memberId}", name="kick")
     */
    public function kick(int $id, int $memberId ,GroupeRepository $groupeRepository, Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $groupe = $groupeRepository->find($id);
        $member = $userRepository->find($memberId);

        $form = $this->createForm(KickMemberGroupeType::class, $groupe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $groupe->removeMember($member);
            $entityManager->persist($groupe);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur expulsé !');
            return $this->redirectToRoute('groupe_view', ['id' => $id]);
        }


        return $this->render('groupe/kick.html.twig', [
            'groupe' => $groupe,
            'form' => $form->createView(),
            'user' => $member,
        ]);
    }
}
