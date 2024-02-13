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

        $resultats = $produitRepository->searchProduct($nom, $categorie);
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
     * @Route("/publish", name="publish")
     */
    public function publish(HubInterface $hub): Response
    {
        $update = new Update(
            'https://example.com/books/1',
            json_encode(['status' => 'ok'])
        );

        $hub->publish($update);

        return new Response('published!');
    }

}