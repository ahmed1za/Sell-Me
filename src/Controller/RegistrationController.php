<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ModifProfilFormType;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\AppAuthenticator;
use App\Services\MailSender;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request,
                             UserPasswordHasherInterface $userPasswordHasher,
                             UserAuthenticatorInterface $userAuthenticator,
                             AppAuthenticator $authenticator,
                             EntityManagerInterface $entityManager,
                             MailSender $sender
                            ): Response

    {
        $user = new User();
        $user->setRoles(["ROLE_USER"]);
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $sender->NotificationDInscription($user);

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/profil", name="app_profil")
     */
    public function profil():Response{
        return $this->render('user/profil.html.twig');
    }

    /**
     * @Route("/modifierProfil", name="app_profil_update")
     */
    public function modifProfil(Request $request, EntityManagerInterface $entityManager):Response{

        $user = $this->getUser();

        if (!$user)
        {
            $this->redirectToRoute("app_login");
        }

        $modifForm= $this->createForm(ModifProfilFormType::class, $user);
        $modifForm->handleRequest($request);
        if ($modifForm->isSubmitted() && $modifForm->isValid()){
            $photoDeProfil = $modifForm->get("photoDeProfil")->getData();

            if (isset($photoDeProfil)){
                $fichier = md5(uniqid()) . '.' . $photoDeProfil->guessExtension();
                $photoDeProfil->move(
                    $this->getParameter('photo_de_profil_directory'),
                    $fichier
                );
                $user->setPhotoDeProfil($fichier);

            }
            $entityManager->flush();

            return $this->redirectToRoute("app_profil");
        }




        return $this->render('user/modifProfil.html.twig',[
            'modifForm'=>$modifForm->createView()
        ]);
    }

    /**
     * @Route("profilVendeur/{id}", name="profil_vendeur")
     */
    public function profilVendur($id, UserRepository $userRepository){
        $user = $userRepository->find($id);

        if (!$user){
            return error_log("Utilisateur introuvable");
        }

        return $this->render("user/profilVendeur.html.twig",[
            "user"=>$user
        ]);

    }

}
