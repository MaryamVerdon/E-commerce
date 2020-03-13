<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ClientRegistrationType;
use App\Service\Mailer\MailerService;
use App\Entity\Client;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, MailerService $mailerService)
    {
        $client = new Client();

        $form = $this->createForm(ClientRegistrationType::class, $client);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $password = $passwordEncoder->encodePassword($client, $client->getPlainPassword());
            $client->setPassword($password);
            $client->setRoles(['ROLE_USER']);

            $client->setConfirmationToken($this->generateToken());
            $client->setActif(false);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($client);
            $entityManager->flush();

            $mailerService->sendRegisteringConformation($client);

            $this->addFlash('user-error', 'Votre inscription a été validée, vous allez recevoir un email de confirmation pour activer votre compte et pouvoir vous connecter');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView(),
            ]);
    }

    /**
     * @Route("/compte/confirm/{token}/{email}", name="app_register_confirm")
     */
    public function confirmRegister($token, $email)
    {
        $email = urldecode($email);
        $em = $this->getDoctrine()->getManager();
        $client = $em->getRepository(Client::class)->findOneBy(['email' => $email]);
        if($client){
            $bdToken = $client->getConfirmationToken();
            if($bdToken === $token){
                $client->setConfirmationToken(null);
                $client->setActif(true);
                $em->persist($client);
                $em->flush();
                return $this->redirectToRoute('app_login');
            }
            return $this->redirectToRoute('app_login');
        }
        return $this->redirectToRoute('app_register');
    }

    /**
     * @Route("/send_new_confirmation_token", name="send_new_confirmation_token")
     */
    public function sendNewConfirmationToken()
    {

    }

    private function generateToken()
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }
}
