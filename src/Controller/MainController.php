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
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class MainController extends AbstractController
{
    /**
     * @Route("/",name="main_home")
     */
public function home(CategoriesRepository $categoriesRepository,Request $request,ProduitRepository $produitRepository){

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






    return  $this->render('main/home.html.twig',[
           'searchForm' => $searchForm->createView(),
        'categories'=>$categories,
           ]);
}

/**
 * @Route("/nous_connaitre",name="nous_connaitre")
 */
public function nousConnaitre()
{
    return $this->render('main/nous_connaitre.html.twig');
}




}