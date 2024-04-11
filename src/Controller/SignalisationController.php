<?php

namespace App\Controller;

use App\Data\Filtre;
use App\Entity\Signalisation;
use App\Form\BlockUserFormType;
use App\Form\FiltreType;
use App\Form\SearchProduitType;
use App\Form\SignaleType;
use App\Repository\CategoriesRepository;
use App\Repository\ProduitRepository;
use App\Repository\SignalisationRepository;
use App\Repository\UserRepository;
use App\Services\MailSender;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

class SignalisationController extends AbstractController
{


    /**
     * @Route("/signalisation_create/{id}/{idProduit}", name="signalisation_create")
     */
    public function create($id, $idProduit, UserRepository $userRepository, ProduitRepository $produitRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->redirectToRoute("app_login");
        }

        $userSignalee = $userRepository->find($id);
        $produit = $produitRepository->find($idProduit);

        $signale = new Signalisation();
        $signale->setUtilisateurQuiSignale($user);
        $signale->setUtilisateurSignale($userSignalee);
        $signale->setProduit($produit);

        $signaleForm = $this->createForm(SignaleType::class, $signale);
        $signaleForm->handleRequest($request);

        if ($signaleForm->isSubmitted() && $signaleForm->isValid()) {
            $signale->setDateSignalement(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));
            $signale->setEtat("en attente");
            $entityManager->persist($signale);
            $entityManager->flush();

            return $this->redirectToRoute('app_message');
        }


        return $this->render('signalisation/create.html.twig', [
            'signaleForm' => $signaleForm->createView(),
            'userSignalee' => $userSignalee
        ]);
    }

    /**
     * @Route("/listSignalement",name="listSignalement")
     */

    public function list(SignalisationRepository $signalisationRepository, ProduitRepository $produitRepository, CategoriesRepository $categoriesRepository, Request $request)
    {
        $list = $signalisationRepository->findUserSignalee();


        $categories = $categoriesRepository->findSixCategories();
        $searchForm = $this->createForm(SearchProduitType::class);
        $searchForm->handleRequest($request);

        $filtre = new Filtre();
        $filreForm = $this->createForm(FiltreType::class, $filtre);
        $filreForm->handleRequest($request);

        if ($filreForm->isSubmitted() && $filreForm->isValid()) {
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
                'categories' => $categories,
                'filtreForm' => $filreForm->createView()
            ]);
        }


        return $this->render("signalisation/list.html.twig", [
            "list" => $list,
            "searchForm" => $searchForm->createView(),
            "categories" => $categories
        ]);
    }

    /**
     * @Route("/gestionSignal/{id}", name="gestionSignal")
     */
    public function gererLeSignalement($id, SignalisationRepository $signalisationRepository, ProduitRepository $produitRepository, CategoriesRepository $categoriesRepository, Request $request)
    {

        $signalement = $signalisationRepository->find($id);
        $user1 = "";
        $user2 = "";


        if (!$signalement) {
            return error_log("signalement non existant");
        } else {
            if ($signalement->isAccesMessage()) {
                $user1 = $signalement->getUtilisateurQuiSignale()->getId();
                $user2 = $signalement->getUtilisateurSignale()->getId();
            }
        }


        $categories = $categoriesRepository->findSixCategories();
        $searchForm = $this->createForm(SearchProduitType::class);
        $searchForm->handleRequest($request);

        $filtre = new Filtre();
        $filreForm = $this->createForm(FiltreType::class, $filtre);
        $filreForm->handleRequest($request);

        if ($filreForm->isSubmitted() && $filreForm->isValid()) {
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
                'categories' => $categories,
                'filtreForm' => $filreForm->createView()
            ]);
        }


        return $this->render('signalisation/gestionSignal.html.twig', [
            "signalement" => $signalement,
            'categories' => $categories,
            "searchForm" => $searchForm->createView(),
            'user1' => $user1,
            'user2' => $user2
        ]);
    }

    /**
     * @Route("/supprimerUtilisateur/{id}/{idSignal}",name="supprimer_utilisateur")
     */
    public function supprimer($id, $idSignal, SignalisationRepository $signalisationRepository, UserRepository $userRepository, EntityManagerInterface $entityManager, MailSender $mailSender)
    {
        $user = $userRepository->find($id);
        $signalement = $signalisationRepository->find($idSignal);
        $motif = $signalement->getMotif();
        if (!$user) {
            return new Response(' <h1> 404 Error</h1>Utilisateur non trouvé', Response::HTTP_NOT_FOUND);
        }

        $mailSender->NotificationSuppression($user->getEmail(), $motif);
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute("listSignalement");
    }

    /**
     * @Route("/ignorer_signalement/{id}",name="ignorer_signalement")
     */
    public function ignorer($id, SignalisationRepository $signalisationRepository, EntityManagerInterface $entityManager,MailSender $mailSender)
    {

        $signalement = $signalisationRepository->find($id);
        $user = $signalement->getUtilisateurQuiSignale()->getEmail();

        $mailSender->notificationIgnorer($user);

        $signalement->setEtat("ignorer");

        $entityManager->flush();

        return $this->redirectToRoute("listSignalement");
    }

    /**
     * @Route("/bloquer_utilisateur/{id}",name="bloquer_utilisateur")
     */
    public function bloquerUtilisateur($id, SignalisationRepository $signalisationRepository, EntityManagerInterface $entityManager,MailSender $mailSender,Request $request):Response
    {
        $signalement = $signalisationRepository->find($id);
        $userABloquer = $signalement->getUtilisateurSignale();
        $userMail = $userABloquer->getEmail();

        $form = $this->createForm(BlockUserFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $duree = $form->get('duree')->getData();
            $dateExpiration = new \DateTime();
            $dateExpiration->modify("+".$duree);

            $mailSender->notificationBlocage($userMail,$dateExpiration);

            $userABloquer->setBloquer(true)
                            ->setDateExpirationBlocage($dateExpiration);

            $signalement->setEtat("utilisateur bloquer");

            $entityManager->flush();

            return $this->redirectToRoute("listSignalement");
        }

        return $this->render("signalisation/blocage.html.twig",[
            "formBlock"=>$form->createView()
        ]);
    }




}
