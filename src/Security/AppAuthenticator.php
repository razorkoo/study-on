<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use App\Security\BillingUser;
use App\Service\BillingClient;

class AppAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;

    private $urlGenerator;
    private $csrfTokenManager;
    private $biilingClient;
    public function __construct(UrlGeneratorInterface $urlGenerator, CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    public function supports(Request $request)
    {
        return 'app_login' === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        $credentials = [
            'email' => $request->request->get('email'),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['email']
        );

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        $billingClient = new BillingClient("billing.study-on.local");
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        try {
            $login = $billingClient->login($credentials['email'], $credentials['password']);
        } catch (\HttpException $ex) {
            throw new CustomUserMessageAuthenticationException("Сервис временно недоступен. Попробуйте авторизоваться позднее");
        }

        if (array_key_exists('code', $login)) {
            throw new CustomUserMessageAuthenticationException($login['message']);
        } else {
            $user = $userProvider->loadUserByUsername($credentials['email']);
            $user->setToken($login['token']);
            $user->setRoles($login['roles']);
        }
        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {

        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        // For example : return new RedirectResponse($this->urlGenerator->generate('some_route'));
        return new RedirectResponse($this->urlGenerator->generate('course_index'));
    }

    protected function getLoginUrl()
    {
        return $this->urlGenerator->generate('app_login');
    }
}
