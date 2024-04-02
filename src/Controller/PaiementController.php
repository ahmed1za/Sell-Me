<?php

namespace App\Controller;


use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use App\Services\PaymentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaiementController extends AbstractController
{




    /**
     * @Route("/stripe/{id}", name="stripe")
     */
    public function stripeCheckOut( $id,
                                    PaymentService $paymentService,
                                    CommandeRepository $commandeRepository,
                                    ProduitRepository $produitRepository,
                                    UrlGeneratorInterface $generator) : RedirectResponse{

        $commande = $commandeRepository->find($id);

        $data = $paymentService->createStripeCheckoutSession($commande,$commandeRepository,$produitRepository,$generator);

        return $data;

    }



    /**
     * @Route("/stripe/success/{id}", name="stripe_success")
     */
    public function stripeSuccesse(PaymentService $paymentService,$id,CommandeRepository $commandeRepository,EntityManagerInterface $entityManager) : Response{
        $data = $paymentService->handleStripeSuccess($id,$commandeRepository,$entityManager);
        $commande = $commandeRepository->find($id);
        $commandeDetails = $commande->getCommandeDetails();



        return $this->render("commande/success.html.twig",[
            "data"=>$data,
            "commande"=>$commande
        ]);

    }


    /**
     * @Route("/stripe/error/{id}", name="stripe_error")
     */
    public function stripeError(PaymentService $paymentService, $id) : Response{

        $data = $paymentService->handleStripeError($id);
        return $this->render("commande/error.html.twig");
    }


}






