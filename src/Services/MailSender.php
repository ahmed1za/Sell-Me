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

    public function NotificationDInscription(UserInterface $user): void
    {

//file_put_contents("test.txt",$user->getEmail());

        $message = new Email();
        $message->from("comptes@sell-me.com")
            ->to("admin@sell-me.com")
            ->subject("nouveau compte")
            ->html("<h1>Nouveau compte</h1><article>nouveau compte vient d'etre créer !!!
                            utilisateur</article>" . $user->getEmail());


        $this->mailer->send($message);
    }

    public function NotificationDeRefus($user, $annonce): void
    {


        $message = new Email();
        $message->from("admin@sell-me.com")
            ->to($user)
            ->subject("refus de votre annonce")
            ->html("<h1>Annonce : </h1>" . $annonce . "<article>Bonjour votre annonce a été examiner, le produit que vous essayer
                            de vendre ou bien les images utiliser sont en désacord avec nos condition general de vente </article>");


        $this->mailer->send($message);
    }

    public function NotificationDeValidation($user, $annonce): void
    {


        $message = new Email();
        $message->from("admin@sell-me.com")
            ->to($user)
            ->subject("Annonce en ligne")
            ->html("<h1>Annonce : </h1>. $annonce . <article>Bonjour , nous avons le plaisir de vous
                            annoncer que votre publication a été mise en ligne </article>");


        $this->mailer->send($message);
    }

    public function NotificationSuppression($user, $motif): void
    {

        $message = new Email();
        $message->from("admin@sell-me.com")
            ->to($user)
            ->subject("Suppression de votre compte")
            ->html("<h1>Information : </h1><article>Bonjour , nous vous informant suite
                            a un signalement sur votre compte et apres vérifcation et examen
                            de vos activité nous avons décider de supprimer votre compte de notre
                            palteforme pour le motif suivant : </article>" . $motif);


        $this->mailer->send($message);
    }

    public function notificationIgnorer($user){
        $message = new Email();
        $message->from("admin@sell-me.com")
            ->to($user)
            ->subject("signalement")
            ->html("<h1>notification</h1> Bonjour suite a votre signalement et exmen 
                            de ce dernier nous n'avons pas trouver des élément pouvons conduire 
                            a une punition ,nous avons ansi décider de ne pas prendre des mesure 
                            a l'encotre de l'utilisateur signalé");

        $this->mailer->send($message);
    }

    public function notificationBlocage($user,$date){
        $message = new Email();
        $message->from("admin@sell-me.com")
            ->to($user)
            ->subject("Blocage")
            ->html("<h1>Votre compte est bloquer</h1> Bonjour suite au signalement émit a votre encontre et exmen 
                            de ce dernier nous n'avons pas trouver des élément pouvons conduire 
                            a une punition ,nous avons ansi décider de suspendre votre compte temporairement, la date limite 
                            de la suspension est ainsi fixer a :".$date->format('Y-m-d H:i:s'));

        $this->mailer->send($message);
    }


}