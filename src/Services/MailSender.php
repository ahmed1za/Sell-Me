<?php

namespace App\Services;


use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Core\User\UserInterface;
use function Symfony\Component\String\u;

class MailSender
{
    protected $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function NotificationDInscription(UserInterface $user) : void{

//file_put_contents("test.txt",$user->getEmail());

        $message = new Email();
        $message->from("comptes@sell-me.com")
            ->to("admin@sell-me.com")
            ->subject("nouveau compte")
            ->html("<h1>Nouveau compte</h1><article>nouveau compte vient d'etre créer !!! utilisateur</article>" . $user->getEmail());


$this->mailer->send($message);
}

public function NotificationDeRefus($user,$annonce) : void{



        $message = new Email();
        $message->from("admin@sell-me.com")
            ->to($user)
            ->subject("refus de votre annonce")
            ->html("<h1>Annonce : </h1>". $annonce ."<article>Bonjour votre annonce a été examiner, le produit que vous essayer
de vendre ou bien les images utiliser sont en désacord avec nos condition general de vente </article>" );


$this->mailer->send($message);
}

    public function NotificationDeValidation($user,$annonce) : void{

//file_put_contents("test.txt",$user->getEmail());

        $message = new Email();
        $message->from("admin@sell-me.com")
            ->to($user)
            ->subject("Annonce en ligne")
            ->html("<h1>Annonce : </h1>. $annonce . <article>Bonjour , nous avons le plaisir de vous annoncer que votre publication a été mise en ligne </article>");


        $this->mailer->send($message);
    }


}