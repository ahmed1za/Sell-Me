<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ContacterVendeurController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("/contacterVendeur/{id}/{produitId}", name="contacterVendeur")
     */
    public function contacterVendeur(int $id, int $produitId): Response
    {
        $user = $this->getUser();

        if (!$user){
            $this->session->set('contacter_vendeur',['id'=>$id,'produitId'=>$produitId]);

            return $this->redirectToRoute('app_login');
        }

        return $this->redirectToRoute('app_chat', [
            'id'=>$id,
            'produitId'=>$produitId
        ]);
    }
}
