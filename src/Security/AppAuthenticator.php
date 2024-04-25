<?php

namespace App\Security;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class AppAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private UrlGeneratorInterface $urlGenerator;
    private  $entityManager;

    public function __construct(UrlGeneratorInterface $urlGenerator,EntityManagerInterface $entityManager)
    {
        $this->urlGenerator = $urlGenerator;
        $this->entityManager = $entityManager;
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email'=> $email]);

        if (!$user){
            throw new CustomUserMessageAuthenticationException("l'email ou le mot de passe est incorrect !");
        }

        if ($user && $user->isBloquer()) {
            if ($user->getDateExpirationBlocage() >= new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'))){
            throw new CustomUserMessageAuthenticationException("Votre compte est bloqué jusqu'au " . $user->getDateExpirationBlocage()->format('Y-m-d H:i:s'));
        }
        }

        $request->getSession()->set(Security::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                new RememberMeBadge()
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }


        $user = $token->getUser();

        if ($user instanceof User && $user->isBloquer() && $user->getDateExpirationBlocage() <= new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'))) {
            $user->setBloquer(false)
                ->setDateExpirationBlocage(null);

            $this->entityManager->flush();
        }

        $contacterVendeurInfo = $request->getSession()->get('contacter_vendeur');

        if ($contacterVendeurInfo) {
            $id = $contacterVendeurInfo['id'];
            $produitId = $contacterVendeurInfo['produitId'];
            $request->getSession()->remove('contacter_vendeur');
            return new RedirectResponse($this->urlGenerator->generate('app_chat', [
                'id' => $id,
                'produitId' => $produitId
            ]));
        }

        // For example:
         return new RedirectResponse($this->urlGenerator->generate('produits_list'));

    }

    protected function getLoginUrl(Request $request): string
    {

        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
