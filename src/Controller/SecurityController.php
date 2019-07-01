<?php

namespace App\Controller;

use App\Security\AppAuthenticator;
use App\Form\RegistrationFormType;
use App\Service\BillingClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Security\BillingUser;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $auth = $this->get('security.authorization_checker');
        if ($auth->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('profile');
        }
        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, BillingClient $billingClient ,GuardAuthenticatorHandler $guardHandler, AppAuthenticator $authenticator)
    {
        $auth = $this->get('security.authorization_checker');
        if ($auth->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('profile');
        } else {
            $form = $this->createForm(RegistrationFormType::class);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                if ($data['password']!=$data['confirmation']) {
                    return $this->render('security/register.html.twig', array(
                        'form' => $form->createView(),
                        'error' => "Пароли должны совпадать"
                    ));
                } else {
                    $response = $billingClient->register(trim($data['email']), trim($data['password']));
                    if (array_key_exists('errors', $response)) {
                        return $this->render('security/register.html.twig', array(
                            'form' => $form->createView(),
                            'error' => $response['errors']
                        ));
                    }

                    if (array_key_exists('code', $response)) {
                        return $this->render('security/register.html.twig', array(
                            'form' => $form->createView(),
                            'error' => $response['message']
                        ));
                    } else {
                        $user = new BillingUser();
                        $user->setEmail($data['email']);
                        $user->setToken($response['userToken']);
                        $user->setRoles($response['roles']);
                        return $guardHandler->authenticateUserAndHandleSuccess(
                            $user,
                            $request,
                            $authenticator,
                            'main'
                        );
                    }
                }
            }
            return $this->render('security/register.html.twig', array(
                'form' => $form->createView(),
                'error' => null
            ));
        }
    }
    /**
     * @Route("/logout", name="app_logout", methods={"GET"})
     */
    public function logout()
    {
    }
    /**
     * @Route("/profile", name="app_profile", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function profile(): Response
    {
        return $this->render('security/profile.html.twig');
    }
}
