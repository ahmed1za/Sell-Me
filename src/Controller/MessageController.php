<?php

namespace App\Controller;

use App\Data\Filtre;
use App\Entity\Messages;
use App\Form\FiltreType;
use App\Form\MessageType;
use App\Form\SearchProduitType;
use App\Repository\CategoriesRepository;
use App\Repository\MessagesRepository;
use App\Repository\ProduitRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
    /**
     * @Route("/message", name="app_message")
     */
    public function index(CategoriesRepository $categoriesRepository, ProduitRepository $produitRepository,MessagesRepository $messagesRepository,Request $request): Response
    {
        $user = $this->getUser();

        $conversations = $messagesRepository->findConversation($user,$user);

        $distinctConversations = [];
        foreach ($conversations as $conversation) {
            $conversationKey = $conversation->getProduit()->getId(); // Utilisez un identifiant unique pour chaque conversation
            if (!isset($distinctConversations[$conversationKey])) {
                $distinctConversations[$conversationKey] = $conversation;
            }
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

            return $this->render('message/index.html.twig',[
                'searchForm' => $searchForm->createView(),
                'categories'=>$categories,
                'conversations'=>$distinctConversations
            ]);
        }



     /**
     * @Route("/chat/{id}/{produitId}", name="app_chat")
     */
    public function chat(int $id,int $produitId,CategoriesRepository $categoriesRepository, ProduitRepository $produitRepository,MessagesRepository
        $messagesRepository,UserRepository $userRepository,Request $request, EntityManagerInterface $entityManager,
    HubInterface $hub): Response
    {


        $user = $this->getUser();
        $destinataire = $userRepository->find($id);
        $produit = $produitRepository->find($produitId);

        $messages = $messagesRepository->findMessages($user,$destinataire) ;

            $message = new Messages();
            $message->setEnvoyeur($user);
            $message->setDestinataire($destinataire);
            $message->setTitre($produit->getNom());
            $message->setProduit($produit);
            $formchat = $this->createForm(MessageType::class, $message);



            $formchat->handleRequest($request);

            if ($formchat->isSubmitted() && $formchat->isValid()){
                $message->setDateDeCreation(new \DateTime("+1 hour"));
                $image = $formchat->get('image')->getData();



                if (isset($image)){

                    $fichier = md5(uniqid()) . '.' . $image->guessExtension();
                    $image->move(
                        $this->getParameter('images_directory_chat'),
                        $fichier
                    );

                    $message->setImage($fichier);
                }




                $entityManager->persist($message);
                $entityManager->flush();

                $data = [
                    "message"=>$message->getMessage(),
                    "envoyeur"=>$message->getEnvoyeur()->getId(),
                    "destinataire"=>$message->getDestinataire()->getId(),
                    "image"=>$message->getImage()
                ];


                $update = new Update(
                    'https://mercure.test/chat',
                   json_encode($data)
                );
                $hub->publish($update);
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
                'messages'=>$messages,
                'formchat'=>$formchat->createView(),
                'destinataire'=>$destinataire
            ]);
        }
}
