<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Form\CategoriesType;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/categories", name="categories_")
 */
class CategoriesController extends AbstractController
{

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categorie = new Categories();
        $categorieForm = $this->createForm(CategoriesType::class,$categorie);
        $categorieForm->handleRequest($request);

        if ($categorieForm->isSubmitted() && $categorieForm->isValid()){
            $entityManager->persist($categorie);
            $entityManager->flush();

            return $this->redirectToRoute("categories_create");

        }


        return $this->render('categories/create.html.twig', [
            'categorieForm' => $categorieForm->createView(),

        ]);
    }


    public function ac(CategoriesRepository $categoriesRepository){
        $categories = $categoriesRepository->findAll();

        return $this->render('inc/nav.html.twig',[
            'categories'=>$categories
        ]);

    }


}
