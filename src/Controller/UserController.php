<?php

namespace App\Controller;

use App\Form\ChangePasswordFormType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/profil", name="profile_details")
     */
    public function profile(): Response
    {
        return $this->render('user/profile.html.twig');
    }

    /**
     * @Route("/profile/update", name="profile_update")
     */
    public function update(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $userForm = $this->createForm(UserType::class, $user);

        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()){
            $entityManager->persist($user);
            $entityManager->flush();



            $this->addFlash('success', 'Profil modifié !');
            return $this->redirectToRoute('profile_details');
        }

        return $this->render('user/update.html.twig', [
            'userForm' => $userForm->createView()
        ]);
    }

    /**
     * @Route("/profile/editPassword", name="edit_password")
     */
    public function editPassword(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();

        $form = $this->createForm(ChangePasswordFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Encode the plain password, and set it.
            $encodedPassword = $passwordEncoder->encodePassword(
                $user,
                $form->get('plainPassword')->getData()
            );

            $user->setPassword($encodedPassword);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('main');
        }



        return $this->render('user/editPassword.html.twig', array(

            'form' => $form->createView(),

        ));
    }
}
