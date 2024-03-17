<?php

namespace App\Controller;

use App\Entity\Paiements;
use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\StripeClient;
use \Symfony\Component\HttpFoundation\Request;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaiementController extends AbstractController
{

    private  $stripeSecretKey='sk_test_51Ouwzk2KEdtAwZH5wr9FrKTyLkk25doZVZQptA0jqGVuCxbMJpQg8H5Um2i56KlHals6JLHSlauWK9yegyzxW1pA00KkJAWYVB';

    /**
     * @Route("/stripe/{id}", name="stripe")
     */
    public function stripeCheckOut($id, CommandeRepository $commandeRepository, ProduitRepository $produitRepository , UrlGeneratorInterface $generator) : RedirectResponse{
        $produitStripe = [];

        $commande = $commandeRepository->find($id);

        if (!$commande){
            return $this->redirectToRoute('cart_index');
        }

        foreach ($commande->getCommandeDetails()->getValues() as $produit){
            $produitId = $produit->getProduitId();
          $produitData = $produitRepository->find($produitId);


            $produitStripe[] = [
              'price_data' => [
                  'currency'=>'eur',
                  'unit_amount'=>$produitData->getPrix()*100,
                  'product_data'=>['name' => $produitData->getNom()]
              ],
                'quantity'=> $produit->getQuantite()
            ];
        }




        Stripe::setApiKey($this->stripeSecretKey);




        $checkout_session = Session::create([
            'customer_email'=> $this->getUser()->getEmail(),
            'payment_method_types'=> ['card'],
            'line_items' =>
               $produitStripe
            ,
            'mode' => 'payment',
            'success_url' => $generator->generate('stripe_success',[
                'id'=> $commande->getId(),

            ], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $generator->generate('stripe_error',[
                'id'=>$commande->getId()
            ],UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        $this->get('session')->set('checkout_session_id', $checkout_session->id);

        return new RedirectResponse($checkout_session->url);
    }


    /**
     * @Route("/stripe/success/{id}", name="stripe_success")
     */
    public function stripeSuccess($id,CommandeRepository $commandeRepository,EntityManagerInterface $entityManager) : Response{

       $sessionId = $this->get('session')->get('checkout_session_id');
       $session = new StripeClient($this->stripeSecretKey);
       $transaction= $session->checkout->sessions->retrieve($sessionId,[]);
       $commande = $commandeRepository->find($id);



       $paiement = new Paiements();
       $paiement->setReference($transaction->payment_intent);
       $paiement->setMontant($transaction->amount_total/100);
       $paiement->setDateDePaiment(new \DateTime('+1 hour'));
       $commande->setPaiements($paiement);

       $entityManager->persist($paiement);
       $entityManager->flush();

        return $this->render("commande/success.html.twig");
    }

    /**
     * @Route("/stripe/error/{id}", name="stripe_error")
     */
    public function stripeError($id) : Response{

        return $this->render("commande/error.html.twig");
    }

}