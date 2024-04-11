<?php

namespace App\Services;

use App\Entity\Commande;
use App\Entity\Paiements;
use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\StripeClient;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class PaymentService
{
    private $stripeSecretKey;
    private $security;
    private $session;
    private $produitRepository;

    public function __construct(string $stripeSecretKey, Security $security,SessionInterface $session,ProduitRepository $produitRepository)
    {
        $this->stripeSecretKey = $stripeSecretKey;
        $this->security = $security;
        Stripe::setApiKey($this->stripeSecretKey);
        $this->session = $session;
        $this->produitRepository = $produitRepository;
    }

    public function createStripeCheckoutSession(
        Commande $commande,
        CommandeRepository $commandeRepository,
        ProduitRepository $produitRepository,
        UrlGeneratorInterface $generator
        ): RedirectResponse
    {
        $produitStripe = [];
        $commandeId = $commande->getId();
        $commande = $commandeRepository->find($commandeId);

        if (!$commande) {
            return new RedirectResponse($generator->generate('cart_index'));
        }

        foreach ($commande->getCommandeDetails()->getValues() as $produit) {
            $produitId = $produit->getProduitId();
            $produitData = $produitRepository->find($produitId);

            $produitStripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $produitData->getPrix() * 100,
                    'product_data' => ['name' => $produitData->getNom()]
                ],
                'quantity' => $produit->getQuantite()
            ];
        }

        $checkout_session = Session::create([
            'customer_email' => $this->security->getUser()->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => $produitStripe,
            'mode' => 'payment',
            'success_url' => $generator->generate('stripe_success', ['id' => $commandeId], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $generator->generate('stripe_error', ['id' => $commandeId], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
        if ($this->session instanceof SessionInterface) {
            $this->session->set('checkout_session_id', $checkout_session->id);
        }
        return new RedirectResponse($checkout_session->url);
    }

    public function handleStripeSuccess($id, CommandeRepository $commandeRepository, EntityManagerInterface $entityManager)
    {
        if ($this->session instanceof SessionInterface && $this->session->has('checkout_session_id')) {
            $sessionId = $this->session->get('checkout_session_id');
            $session = new StripeClient($this->stripeSecretKey);
            $transaction = $session->checkout->sessions->retrieve($sessionId, []);
            $commande = $commandeRepository->find($id);

            if (!$commande){
                return ['success'=>false,'message'=>"commande introuvable"];
            }

            foreach ($commande->getCommandeDetails() as $detail){
                $produitId = $detail->getProduitId();
                $quantiteCommandee = $detail->getQuantite();
                $produit = $this->produitRepository->find($produitId);

                if (!$produit) {
                    return ['success' => false, 'message' => 'Produit introuvable'];
                }

                if ($quantiteCommandee > $produit->getQuantite()) {
                    return ['success' => false, 'message' => 'Quantité insuffisante en stock pour le produit ' . $produit->getNom()];
                }

                $nouvelleQuantite = $produit->getQuantite() - $quantiteCommandee;
                $produit->setQuantite($nouvelleQuantite);

            }

            $entityManager->flush();

            $paiement = new Paiements();
            $paiement->setReference($transaction->payment_intent);
            $paiement->setMontant($transaction->amount_total / 100);
            $paiement->setDateDePaiment(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));
            $commande->setPaiements($paiement);

            $entityManager->persist($paiement);
            $entityManager->flush();
        }

        return $paiement;
    }

    public function handleStripeError($id)
    {
        return ['success' => false];
    }
}
