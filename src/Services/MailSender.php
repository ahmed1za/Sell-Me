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
}