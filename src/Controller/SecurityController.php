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
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


use App\Security\Token\ClientToken;
use App\Security\Token\JWT;

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

            /*
            $client->setConfirmationToken($this->generateToken());
            */
            $client->setConfirmationToken((ClientToken::create($client,86400))->getToken());
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
    /*
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
            $this->addFlash('user-error', 'Lien éronné');
            return $this->redirectToRoute('app_login');
        }
        return $this->redirectToRoute('app_register');
    }
    */

    /**
     * @Route("/send_new_confirmation_token/{email}", name="send_new_confirmation_token")
     */
    /*
    public function sendNewConfirmationToken($email, MailerService $mailerService)
    {
        $email = urldecode($email);
        $em = $this->getDoctrine()->getManager();
        $client = $em->getRepository(Client::class)->findOneBy(['email' => $email]);
        if($client){
            if(!$client->getActif()){
                $client->setConfirmationToken($this->generateToken());
                $em->persist($client);
                $em->flush();
    
                $mailerService->sendRegisteringConformation($client);
    
            }else{
                $this->addFlash('user-error', 'Votre compte est deja actif');
            }
        }else{
            $this->addFlash('user-error', 'Aucun compte pour cet email');
        }
        return $this->redirectToRoute('app_login');
    }
    */

    /**
     * @Route("/compte/confirm", name="app_register_confirm")
     */
    public function confirmRegister(Request $request)
    {
        $token = $request->get("token");
        if($token && ClientToken::validate($token,"abc123")){
            $email = ClientToken::decode($token)["client/email"];
            $em = $this->getDoctrine()->getManager();
            $client = $em->getRepository(Client::class)->findOneBy(['email' => $email]);
            if($client){
                $bdToken = $client->getConfirmationToken();
                if($bdToken === $token){
                    if(ClientToken::dateValid($token)){
                        $client->setConfirmationToken(null);
                        $client->setActif(true);
                        $em->persist($client);
                        $em->flush();
                        return $this->redirectToRoute('app_login');
                    }
                    $this->addFlash('user-error', 'Jeton expiré');
                    return $this->redirectToRoute('app_login');
                }
                $this->addFlash('user-error', 'Lien éronné');
                return $this->redirectToRoute('app_login');
            }
            return $this->redirectToRoute('app_register');
        }
        $this->addFlash('user-error', 'Jeton nul ou invalide');
        return $this->redirectToRoute('app_login');
    }

    /**
     * @Route("/send_new_confirmation_token/{email}", name="send_new_confirmation_token")
     */
    public function sendNewConfirmationToken($email, MailerService $mailerService)
    {
        $email = urldecode($email);
        $em = $this->getDoctrine()->getManager();
        $client = $em->getRepository(Client::class)->findOneBy(['email' => $email]);
        if($client){
            if(!$client->getActif()){
                $client->setConfirmationToken((ClientToken::create($client,86400))->getToken());
                $em->persist($client);
                $em->flush();
    
                $mailerService->sendRegisteringConformation($client);
    
            }else{
                $this->addFlash('user-error', 'Votre compte est deja actif');
            }
        }else{
            $this->addFlash('user-error', 'Aucun compte pour cet email');
        }
        return $this->redirectToRoute('app_login');
    }


    /**
     * @Route("/mot_de_passe_oublie", name="forgotten_password")
     */
    public function forgottenPassword(Request $request, MailerService $mailerService)
    {
        $form = $this->createFormBuilder([])
            ->add('email', EmailType::class)
            ->add('save', SubmitType::class, ['label' => 'Mot de passe oublié'])
            ->getForm();

        
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $client = $em->getRepository(Client::class)->findOneBy(['email' => $form->getData()["email"]]);
            if($client){
                if($client->getActif()){
                    $client->setPasswordToken((ClientToken::create($client,3600))->getToken());
                    $em->persist($client);
                    $em->flush();
        
                    $mailerService->sendPasswordChange($client);
        
                    $this->addFlash('user-error', 'Vous allez recevoir un email de confirmation pour activer votre compte et pouvoir vous connecter');
        
                    return $this->redirectToRoute('app_login');

                }
                return $this->redirectToRoute('app_login');
            }

            $this->addFlash('user-error', 'Aucun compte pour cet email');

            return $this->redirectToRoute('forgotten_password');
        }
        return $this->render('security/forgotten-password.html.twig', [
            'form' => $form->createView(),
            ]);
    }


    /**
     * @Route("/reset_password/{token}", name="reset_password")
     */
    public function resetPassword($token, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {

    }

    /*
    private function generateToken()
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }
    */
}
