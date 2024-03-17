<?php

namespace App\Controller;

use App\Data\Filtre;
use App\Form\FiltreType;
use App\Form\SearchProduitType;
use App\Repository\CategoriesRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/cart", name="cart_")
 */
class CartController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(Request $request,ProduitRepository $produitRepository,CategoriesRepository $categoriesRepository,SessionInterface $session): Response
    {

        $panier = $session->get("panier", []);
        $dataPanier = [];
        $total = 0;

        foreach ($panier as $id => $quantite){
            $produit = $produitRepository->find($id);
            $dataPanier[] = [
                "produit"=>$produit,
                "quantite"=>$quantite
            ];
            $total += $produit->getPrix()*$quantite;
        }

        $categories = $categoriesRepository->findSixCategories();
        $searchForm = $this->createForm(SearchProduitType::class);
        $searchForm->handleRequest($request);

        $filre = new Filtre();
        $filreForm = $this->createForm(FiltreType::class,$filre);
        $filreForm->handleRequest($request);

        if ($filreForm->isSubmitted() && $filreForm->isValid()){
            $produits = $produitRepository->filtrer($filre);
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



        return $this->render('cart/index.html.twig', [
            'searchForm' => $searchForm->createView(),
            'categories'=>$categories,
            'dataPanier'=>$dataPanier,
            'total'=>$total

        ]);
    }

    /**
     * @Route("/add/{id}", name="add")
     */
    public function add($id,SessionInterface $session,ProduitRepository $produitRepository,CategoriesRepository $categoriesRepository,Request $request){
        $produit = $produitRepository->find($id);

        if (!$produit) {
            throw $this->createNotFoundException('Le produit n\'existe pas.');
        }
        
        $panier = $session->get("panier", []);


        if (!empty($panier[$id])) {

            if ($produit->getQuantite() > $panier[$id] && $produit->getVendeur()->getNature() === "professionnel") {

                $panier[$id]++;
            } else {

                $this->addFlash('error-cart', 'La quantité en stock est insuffisante pour ce produit.');
            }
        } else {
            if ($produit->getVendeur()->getNature() === "professionnel") {
                $panier[$id] = 1;
            }
        }

        $totalProduits = array_sum($panier);
        $session->set("panier",$panier);
        $session->set("totalProduits",$totalProduits);




        $categories = $categoriesRepository->findSixCategories();
        $searchForm = $this->createForm(SearchProduitType::class);
        $searchForm->handleRequest($request);

        $filre = new Filtre();
        $filreForm = $this->createForm(FiltreType::class,$filre);
        $filreForm->handleRequest($request);

        if ($filreForm->isSubmitted() && $filreForm->isValid()){
            $produits = $produitRepository->filtrer($filre);
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


        return $this->redirectToRoute('produits_detail',[
            'id'=>$id
        ]);
    }





    /**
     * @Route("/remove/{id}", name="remove")
     */
    public function remove($id,SessionInterface $session,ProduitRepository $produitRepository){
        $produit = $produitRepository->find($id);

        if (!$produit) {
            throw $this->createNotFoundException('Le produit n\'existe pas.');
        }
        $panier = $session->get("panier", []);
        if (!empty($panier[$id])) {

            if ($panier[$id] > 1) {

                $panier[$id]--;
            } else {
                unset($panier[$id]);
            }
        }


        $totalProduits = array_sum($panier);
        $session->set("panier",$panier);
        $session->set("totalProduits",$totalProduits);



        return $this->redirectToRoute('cart_index');
    }


    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete($id,SessionInterface $session,ProduitRepository $produitRepository){
        $produit = $produitRepository->find($id);

        if (!$produit) {
            throw $this->createNotFoundException('Le produit n\'existe pas.');
        }
        $panier = $session->get("panier", []);
        if (!empty($panier[$id])) {

                unset($panier[$id]);
             }


        $totalProduits = array_sum($panier);
        $session->set("panier",$panier);
        $session->set("totalProduits",$totalProduits);



        return $this->redirectToRoute('cart_index');
    }
 /**
     * @Route("/deleteAll", name="deleteAll")
     */
    public function deleteAll(SessionInterface $session){



        $totalProduits = 0;
        $session->remove("panier");
        $session->set("totalProduits",$totalProduits);



        return $this->redirectToRoute('cart_index');
    }


}

