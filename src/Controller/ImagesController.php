<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Produit;
use App\Form\ImagesType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/images",name="images_")
 */
class ImagesController extends AbstractController
{
    /**
     * @Route("/create/{id}", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entityManager, $id, ProduitRepository $produitRepository): Response
    {
        $produit = $produitRepository->find($id);
        if (!$produit) {
            throw $this->createNotFoundException('Produit non trouvé');
        }

        $imageForm = $this->createForm(ImagesType::class);
        $imageForm->handleRequest($request);

        if ($imageForm->isSubmitted() && $imageForm->isValid()) {
            $images = $imageForm->get('image')->getData();

            foreach ($images as $imageFile) {
                $image = new Image();
                $fichier = md5(uniqid()) . '.' . $imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );
                $image->setImage($fichier);
                $image->setProduit($produit);
                $produit->addImage($image);
                $entityManager->persist($image);
            }

            $entityManager->flush();

            return $this->redirectToRoute("produits_list");
        }

        return $this->render('images/create.html.twig', [
            'imageForm' => $imageForm->createView(),
            'produit' => $produit
        ]);
    }

}
