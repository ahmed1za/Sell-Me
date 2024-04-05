<?php

namespace App\Controller;

use App\Entity\Signalisation;
use App\Form\SignaleType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SignalisationController extends AbstractController
{
    /**
     * @Route("/signalisation_create/{id}", name="signalisation_create")
     */
    public function create($id,UserRepository $userRepository,Request $request,EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user)
        {
            $this->redirectToRoute("app_login");
        }

        $userSignalee = $userRepository->find($id);

        $signale = new Signalisation();
        $signale->setUtilisateurQuiSignale($user);
        $signale->setUtilisateurSignale($userSignalee);

        $signaleForm = $this->createForm(SignaleType::class,$signale);
        $signaleForm->handleRequest($request);

        if ($signaleForm->isSubmitted() && $signaleForm->isValid()){
            $signale->setDateSignalement(new \DateTime("+1 hour"));
            $signale->setEtat("en attente");
            $entityManager->persist($signale);
            $entityManager->flush();

            return $this->redirectToRoute('app_message');
        }


        return $this->render('signalisation/create.html.twig', [
            'signaleForm' => $signaleForm->createView(),
            'userSignalee'=>$userSignalee
        ]);
    }
}
