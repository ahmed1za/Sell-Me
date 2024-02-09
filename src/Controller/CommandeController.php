<?php

namespace App\Controller;

use App\Data\Filtre;
use App\Entity\Commande;
use App\Entity\CommandeDetails;
use App\Form\FiltreType;
use App\Form\SearchProduitType;
use App\Repository\CategoriesRepository;
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
        $this->denyAccessUnlessGranted("ROLE_USER");

        $panier = $session->get('panier', []);
        if ($panier === []){
            $this->addFlash('message-commande','votre panier est vide');
            return $this->redirectToRoute('cart_index');
        }

        $commande = new Commande();
        $commande->setUserId($this->getUser());

        $commande->setReference($this->genererRef());
        $commande->setDateDeCreation(new \DateTime("+1 hour"));

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

            $resultats = $produitRepository->searchProduct($nom, $categorie);
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


    public function genererRef(){
        $user = $this->getUser();
        $date = new \DateTime();
        $nombre = rand(100,99999999);

        $reference = $date->format('Y-m-d H:i:s') .'-'.$user->getId().'-'.$nombre;

        return $reference;
    }

}
