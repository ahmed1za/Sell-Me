<?php

namespace App\Controller;

use App\Data\Filtre;
use App\Entity\Produit;
use App\Form\FiltreType;
use App\Form\ProduitType;
use App\Form\SearchProduitType;
use App\Repository\CategoriesRepository;
use App\Repository\ProduitRepository;
use App\Services\HomeService;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Stmt\Foreach_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/produits",name="produits_")
 */
class ProduitController extends AbstractController
{

    /**
     * @Route("", name="list")
     */
    public function list(ProduitRepository $produitRepository,CategoriesRepository $categoriesRepository, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = 30;

        $produits = $produitRepository->findBestProduct($page,$limit);

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




        return $this->render('produit/list.html.twig',[
            "produits"=>$produits ,
            'searchForm' => $searchForm->createView(),
            'categories'=>$categories,
            'filtreForm'=>$filreForm->createView()



        ]);
    }

    /**
     * @Route("/detail/{id}", name="detail")
     */
    public function detail(int $id, ProduitRepository $produitRepository,CategoriesRepository $categoriesRepository,Request $request): Response
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




        return $this->render('produit/detail.html.twig',[
            "produit"=>$produit,
            'searchForm' => $searchForm->createView(),
            'categories'=>$categories,
            'filtreForm'=>$filreForm->createView()
        ]);
    }

    /**
     * @Route("/create", name="create")
     */public function create(Request $request, EntityManagerInterface $entityManager, ParameterBagInterface $parameterBag): Response

        {
        $user = $this->getUser();
        if (!$user){
            return $this->redirectToRoute('app_login');
        }
        $produit = new Produit();
        $produit->setVendeur($user);
        $produitForm = $this->createForm(ProduitType::class, $produit);
        $produitForm->handleRequest($request);
            $image = $produitForm->get('image')->getData();

        if ($produitForm->isSubmitted() && $produitForm->isValid()){
            if ($produit->getVendeur()->getNature() === 'professionnel'){
                $produit->setLivraison(true);
            }

            if ($produit->getVendeur()->getNature() === 'particulier')
            {
                $produit->setEtat("Occasion");
            }

            if (isset($image)){
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );
                $produit->setImage($fichier);
            }


            $produit->setDateDeCreation(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));
            $produit->setDateDeModification(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));
            $produit->setStatut("en attente de validation");
            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('images_create',['id'=>$produit->getId()]);

        }
        return $this->render('produit/create.html.twig', [
            'produiForm'=>$produitForm->createView()
        ]);
    }

    /**
     * @Route("/delete/{id}",name="delete")
     */
    public function delete($id, EntityManagerInterface $entityManager,ProduitRepository $produitRepository){
            $produit = $produitRepository->find($id);

            $entityManager->remove($produit);
            $entityManager->flush();

            return $this->redirectToRoute("main_home");
    }



    /**
     * @Route("/categorie/{categorie}", name="categorie")
     */
    public function findByCategory($categorie, ProduitRepository $produitRepository,CategoriesRepository $categoriesRepository,Request $request): Response
    {

        $produits = $produitRepository->findByCategory($categorie);


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



        return $this->render('produit/list.html.twig',[
            'produits' => $produits,
            'searchForm' => $searchForm->createView(),
            'categories'=>$categories,
            'filtreForm'=>$filreForm->createView()

        ]);
    }












    /**
     * @Route("/mesAnnonces",name="mesAnnonces")
     */

    public function mesAnnonce(ProduitRepository $produitRepository, CategoriesRepository $categoriesRepository,Request $request):Response{
        $user = $this->getUser();

        if ($user){
            $produits = $produitRepository->findBy(['Vendeur'=>$user]);
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

        return $this->render('produit/mesAnnonces.html.twig',[
           'searchForm'=> $searchForm->createView(),
            'categories'=>$categories,
            'produits'=>$produits
        ]);


    }



}

