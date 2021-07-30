<?php

namespace App\Controller;

use App\Form\ChangePasswordFormType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

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
    public function update(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $user = $this->getUser();

        $userForm = $this->createForm(UserType::class, $user);

        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()){
            /** @var UploadedFile $avatar */
            $avatar = $userForm->get('avatar')->getData();

            if ($avatar) {
                $originalFileName = pathinfo($avatar->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFileName = $slugger->slug($originalFileName);
                $newFileName = $safeFileName.'-'.uniqid().'.'.$avatar->guessExtension();

                try {
                    $avatar->move(
                        $this->getParameter('pictures_directory'),
                        $newFileName
                    );
                } catch (FileException $e) {
                    throw new FileException('Erreur lors du téléchargement de l\'image');
                }
                $user->setAvatar($newFileName);
            }
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


    /**
     * @Route("/profil/{username}", name="profile_details_user")
     */
    public function details(string $username, UserRepository $userRepository): Response
    {
        $user = $userRepository->findOneBy(['username' => $username]);

        if (!$user){
            throw $this->createNotFoundException('Cet utilisateur est inexistant.');
        }

        return $this->render('user/details.html.twig', [
            "user" => $user
        ]);
    }
}
