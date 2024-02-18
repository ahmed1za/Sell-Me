<?php

namespace App\Controller;

use App\Data\Filtre;
use App\Entity\Messages;
use App\Form\FiltreType;
use App\Form\MessageType;
use App\Form\SearchProduitType;
use App\Repository\CategoriesRepository;
use App\Repository\ProduitRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
    /**
     * @Route("/message", name="app_message")
     */
    public function index(CategoriesRepository $categoriesRepository, ProduitRepository $produitRepository, Request $request): Response
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

            return $this->render('message/index.html.twig',[
                'searchForm' => $searchForm->createView(),
                'categories'=>$categories,
            ]);
        }



     /**
     * @Route("/chat/{id}/{produitId}", name="app_chat")
     */
    public function chat(int $id,int $produitId,CategoriesRepository $categoriesRepository, ProduitRepository $produitRepository,UserRepository $userRepository,Request $request): Response
    {


        $user = $this->getUser();
        $destinataire = $userRepository->find($id);
        $titre = $produitRepository->find($produitId);

            $message = new Messages();
            $message->setEnvoyeur($user);
            $message->setDestinataire($destinataire);
            $message->setTitre($titre->getNom());
            $formchat = $this->createForm(MessageType::class, $message);

            $formchat->handleRequest($request);

            if ($formchat->isSubmitted() && $formchat->isValid()){
                $message->setDateDeCreation(new \DateTime("+1 hour"));
            }





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




            return $this->render('message/chat.html.twig',[
                'searchForm' => $searchForm->createView(),
                'categories'=>$categories,
                'message'=>$message,
                'titre'=>$titre
            ]);
        }
}
