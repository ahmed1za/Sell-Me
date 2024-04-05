<?php

namespace App\Controller;

use App\Data\Filtre;
use App\Entity\User;
use App\Form\FiltreType;
use App\Form\RegistrationFormType;
use App\Form\SearchProduitType;
use App\Repository\CategoriesRepository;
use App\Repository\ProduitRepository;
use App\Security\AppAuthenticator;
use App\Services\functionService;
use App\Services\MailSender;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin", name="admin_")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function index( CategoriesRepository $categoriesRepository, ProduitRepository $produitRepository,Request $request): Response
    {

        $categories = $categoriesRepository->findSixCategories();
        $searchForm = $this->createForm(SearchProduitType::class);
        $searchForm->handleRequest($request);

        $filtre = new Filtre();
        $filreForm = $this->createForm(FiltreType::class,$filtre);
        $filreForm->handleRequest($request);

        if ($filreForm->isSubmitted() && $filreForm->isValid()){
            $produits = $produitRepository->filtrer($filtre);
        }

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $data = $searchForm->getData();
            $nom = $data['nom'];
            $categorie = $data['categorie'];

            $resultats = $produitRepository->searchProduct($nom, $categorie);
            return $this->render('produit/produitSearch.html.twig', [
                'searchForm' => $searchForm->createView(),
                'resultats' => $resultats,
                'categories'=>$categories,
                'filtreForm'=>$filreForm->createView()
            ]);
        }





        return $this->render('admin/index.html.twig', [
            'searchForm' => $searchForm->createView(),
            'categories' => $categories,

        ]);
    }

    /**
     * @Route("/validation_annonce", name="validation_annonce")
     */
    public function validationAnnonce( CategoriesRepository $categoriesRepository, ProduitRepository $produitRepository,Request $request): Response
    {
        $annonces = $produitRepository->annonceAValider();

        $categories = $categoriesRepository->findSixCategories();
        $searchForm = $this->createForm(SearchProduitType::class);
        $searchForm->handleRequest($request);

        $filtre = new Filtre();
        $filreForm = $this->createForm(FiltreType::class,$filtre);
        $filreForm->handleRequest($request);

        if ($filreForm->isSubmitted() && $filreForm->isValid()){
            $produits = $produitRepository->filtrer($filtre);
        }

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $data = $searchForm->getData();
            $nom = $data['nom'];
            $categorie = $data['categorie'];

            $resultats = $produitRepository->searchProduct($nom, $categorie);
            return $this->render('produit/produitSearch.html.twig', [
                'searchForm' => $searchForm->createView(),
                'resultats' => $resultats,
                'categories'=>$categories,
                'filtreForm'=>$filreForm->createView()
            ]);
        }





        return $this->render('admin/annonce.html.twig', [
            'searchForm' => $searchForm->createView(),
            'categories' => $categories,
            'annonces'=>$annonces

        ]);
    }


    /**
     * @Route("/exam_annonce/{id}", name="exam_annonce")
     */
    public function examAnnonce( $id,ProduitRepository $produitRepository,CategoriesRepository $categoriesRepository,Request $request): Response
    {
       $produit = $produitRepository->find($id);


        $categories = $categoriesRepository->findSixCategories();
        $searchForm = $this->createForm(SearchProduitType::class);
        $searchForm->handleRequest($request);

        $filtre = new Filtre();
        $filreForm = $this->createForm(FiltreType::class,$filtre);
        $filreForm->handleRequest($request);

        if ($filreForm->isSubmitted() && $filreForm->isValid()){
            $produits = $produitRepository->filtrer($filtre);
        }

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $data = $searchForm->getData();
            $nom = $data['nom'];
            $categorie = $data['categorie'];

            $resultats = $produitRepository->searchProduct($nom, $categorie);
            return $this->render('produit/produitSearch.html.twig', [
                'searchForm' => $searchForm->createView(),
                'resultats' => $resultats,
                'categories'=>$categories,
                'filtreForm'=>$filreForm->createView()
            ]);
        }





        return $this->render('admin/examAnnonce.html.twig',[
            "produit"=>$produit,
            "categories"=>$categories,
            "searchForm"=>$searchForm->createView()
        ]);
    }

    /**
     * @Route("annonceOk/{id}",name="annonceOk")
     */

        public function valider($id,ProduitRepository $produitRepository,EntityManagerInterface $entityManager,MailSender $mailSender){
            $produit = $produitRepository->find($id);
            $userEmail = $produit->getVendeur()->getEmail();

            if (!$produit){
                return $this->redirectToRoute('admin_validation_annonce');
            }

            $produit->setStatut("en ligne");
            $entityManager->flush();

            $mailSender->NotificationDeValidation($userEmail,$produit->getNom());

            return $this->redirectToRoute('admin_validation_annonce');
        }

        /**
     * @Route("annonceRemove/{id}",name="annonceRemove")
     */

        public function refuser($id,ProduitRepository $produitRepository,EntityManagerInterface $entityManager,MailSender $mailSender){
            $produit = $produitRepository->find($id);
            $userEmail = $produit->getVendeur()->getEmail();

            if (!$produit){
                return $this->redirectToRoute('admin_validation_annonce');
            }

            $produit->setStatut("produit refuser");
            $mailSender->NotificationDeRefus($userEmail,$produit->getNom());
            $entityManager->remove($produit);

            $entityManager->flush();





            return $this->redirectToRoute('admin_validation_annonce');
        }

        /**
         * @Route ("ajouterUtilisateur",name="ajouterUtilisateur")
         */
        public function ajouterUtilisateur(Request $request,
                                           UserPasswordHasherInterface $userPasswordHasher,
                                           EntityManagerInterface $entityManager,
                                            MailSender $mailSender):Response
        {
            $utilisateur = new User();
            $utilisateur->setRoles(["ROLE_USER"]);
            $userForm = $this->createForm(RegistrationFormType::class,$utilisateur);
            $userForm->handleRequest($request);

            if ($userForm->isSubmitted() && $userForm->isValid()) {
                $utilisateur->setPassword(
                    $userPasswordHasher->hashPassword(
                        $utilisateur,
                        $userForm->get('plainPassword')->getData()
                    )
                );

                $entityManager->persist($utilisateur);
                $entityManager->flush();

                $mailSender->NotificationDInscription($utilisateur);
                $this->addFlash("user-add","utilisateur ajouté avec succes");
                return $this->redirectToRoute("admin_dashboard");

            }

            return $this->render('registration/register.html.twig', [
                'registrationForm' => $userForm->createView(),
            ]);


            }



            /**
         * @Route ("ajouterAdmin",name="ajouterAdmin")
         */
        public function ajouterAdmin(Request $request,
                                           UserPasswordHasherInterface $userPasswordHasher,
                                           EntityManagerInterface $entityManager,
                                            MailSender $mailSender):Response
        {
            $utilisateur = new User();
            $utilisateur->setRoles(["ROLE_ADMIN"]);
            $userForm = $this->createForm(RegistrationFormType::class,$utilisateur);
            $userForm->handleRequest($request);

            if ($userForm->isSubmitted() && $userForm->isValid()) {
                $utilisateur->setPassword(
                    $userPasswordHasher->hashPassword(
                        $utilisateur,
                        $userForm->get('plainPassword')->getData()
                    )
                );

                $entityManager->persist($utilisateur);
                $entityManager->flush();

                $mailSender->NotificationDInscription($utilisateur);
                $this->addFlash("user-add","Admin ajouté avec succes");
                return $this->redirectToRoute("admin_dashboard");

            }

            return $this->render('registration/register.html.twig', [
                'registrationForm' => $userForm->createView(),
            ]);


            }



            /**
         * @Route ("supprimerUtilisateur",name="supprimerUtilisateur")
         */
        public function supprimerUtilisateur(Request $request,
                                           UserPasswordHasherInterface $userPasswordHasher,
                                           EntityManagerInterface $entityManager,
                                            MailSender $mailSender):Response
        {
            $utilisateur = new User();
            $utilisateur->setRoles(["ROLE_ADMIN"]);
            $userForm = $this->createForm(RegistrationFormType::class,$utilisateur);
            $userForm->handleRequest($request);

            if ($userForm->isSubmitted() && $userForm->isValid()) {
                $utilisateur->setPassword(
                    $userPasswordHasher->hashPassword(
                        $utilisateur,
                        $userForm->get('plainPassword')->getData()
                    )
                );

                $entityManager->persist($utilisateur);
                $entityManager->flush();

                $mailSender->NotificationDInscription($utilisateur);
                $this->addFlash("user-add","Admin ajouté avec succes");
                return $this->redirectToRoute("admin_dashboard");

            }

            return $this->render('registration/register.html.twig', [
                'registrationForm' => $userForm->createView(),
            ]);


            }
}
