<?php

namespace App\Controller;

use App\Data\Filtre;
use App\Entity\Commande;
use App\Entity\CommandeDetails;
use App\Form\FiltreType;
use App\Form\SearchProduitType;
use App\Repository\CategoriesRepository;
use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/commandes", name="commandes_")
 */
class CommandeController extends AbstractController
{
    /**
     * @Route("/ajout", name="add")
     */
    public function add(SessionInterface $session,
                        ProduitRepository $produitRepository,
                        EntityManagerInterface $entityManager,
                        CategoriesRepository $categoriesRepository,
                        Request $request
    ): Response
    {
       // $this->denyAccessUnlessGranted(["ROLE_ADMIN","ROLE_USER"]);

        $panier = $session->get('panier', []);
        if ($panier === []){
            $this->addFlash('message-commande','votre panier est vide');
            return $this->redirectToRoute('cart_index');
        }

        $commande = new Commande();
        $commande->setUserId($this->getUser());

        $commande->setReference($this->genererRef());
        $commande->setDateDeCreation(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));

        foreach ($panier as $item => $quantite){
            $commandeDetails = new CommandeDetails();

            $produit = $produitRepository->find($item);
            $prix = $produit->getPrix() * $quantite;

            $commandeDetails->setProduitId($produit);
            $commandeDetails->setPrix($prix);
            $commandeDetails->setQuantite($quantite);

            $commande->addCommandeDetail($commandeDetails);
        }

        $entityManager->persist($commande);
        $entityManager->flush();

        $session->remove('panier');


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
            $page = $request->query->getInt('page',1);
            $resultats = $produitRepository->searchProduct($nom, $categorie,$page);
            return $this->render('produit/produitSearch.html.twig', [
                'searchForm' => $searchForm->createView(),
                'resultats' => $resultats,
                'categories'=>$categories,
                'filtreForm'=>$filreForm->createView()
            ]);
        }






        return $this->render('commande/index.html.twig', [
            'commande' => $commande,
            'searchForm' => $searchForm->createView(),
            'categories'=>$categories,
        ]);
    }

    /**
     * @Route("/list",name="list")
     */
    public function listCommandes(CommandeRepository $commandeRepository, ProduitRepository $produitRepository,CategoriesRepository $categoriesRepository, Request $request){

        $user = $this->getUser();
        $userId = $user->getId();

        if ($user){
            $commandes = $commandeRepository->findByUser($userId);
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
            $page = $request->query->getInt('page',1);
            $resultats = $produitRepository->searchProduct($nom, $categorie,$page);
            return $this->render('produit/produitSearch.html.twig', [
                'searchForm' => $searchForm->createView(),
                'resultats' => $resultats,
                'categories'=>$categories,
                'filtreForm'=>$filreForm->createView()
            ]);
        }




        return $this->render("commande/list.html.twig",[
            'commandes'=>$commandes,
            'searchForm' => $searchForm->createView(),
            'categories'=>$categories,
        ]);

    }

    /**
     * @Route("/detail/{id}",name="detail")
     */
    public function detail($id, ProduitRepository $produitRepository,CategoriesRepository $categoriesRepository, CommandeRepository $commandeRepository,Request $request){

      $commande = $commandeRepository->find($id);

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
            $page = $request->query->getInt('page', 1);
            $resultats = $produitRepository->searchProduct($nom, $categorie,$page);
            return $this->render('produit/produitSearch.html.twig', [
                'searchForm' => $searchForm->createView(),
                'resultats' => $resultats,
                'categories'=>$categories,
                'filtreForm'=>$filreForm->createView()
            ]);
        }





        return $this->render('commande/index.html.twig', [
            'commande' => $commande,
            'searchForm' => $searchForm->createView(),
            'categories'=>$categories,
        ]);
    }

    /**
     * @Route("/commandePayee/{id}",name="payee")
     */
    public function commandePayee($id,CommandeRepository $commandeRepository){
        $commande = $commandeRepository->find($id);

        return $this->render("commande/commandeValide.html.twig",[
            'commande'=>$commande
        ]);
    }




    public function genererRef(){
        $user = $this->getUser();
        $date = new \DateTime();
        $nombre = rand(100,99999999);

        $reference = $date->format('Y-m-d H:i:s') .'-'.$user->getId().'-'.$nombre;

        return $reference;
    }

}
