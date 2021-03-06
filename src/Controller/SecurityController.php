<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ResetPassType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
     

        // erreur de connexion
        $error = $authenticationUtils->getLastAuthenticationError();
        // dernier nom rempli par l'utilisateur
        $lastUsername = $authenticationUtils->getLastUsername();
        // on envoie ça à la vue ainsi que l'error
        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
    /**
     * @Route("/forgotten_pass", name="forgotten_pass")
     */
    public function forgottenPass(Request $request, UserRepository $userRepo, \Swift_Mailer $mailer, TokenGeneratorInterface $tokenGenerator)
    {
        // on cree le form
        $form = $this->createForm(ResetPassType :: class);
        // on fait le traitement
        $form->handleRequest($request);
        // si le form est valide 
        if ($form->isSubmitted() && $form->isValid()) {
            // on recupere les datas
            $donnees = $form->getData();
            // on verifie l'email en bdd
            $user = $userRepo->findOneByEmail($donnees['email']);
            // si lutil nexiste pas message flash
            if (!$user) {
                $this->addFlash('danger', 'Un compte avec cet adresse email n\'existe pas');
                return $this->redirectToRoute('app_login');
            }
            // si lutil existe on genere notre token de récuperationde mdp
            $token = $tokenGenerator->generateToken();
            try {
                $user->setResetToken($token);
                 // on fait appel à doctrine pour enregistrer en base de données
                $em = $this->getDoctrine()->getManager();
                // on prepare la mise en BDD
                $em->persist($user);
                // on sauvegarde en BDD
                $em->flush();
            } 
            catch (\Exception $error) { 
                // on envoie un flash avec l'erreur
                $this->addFlash('warning', `Une erreur est survenue :` . $error->getMessage());
                // on retourne à la page de connexion
                return $this->redirectToRoute('app_login');
            }
            //    generation de l'url de reinitialisation de mdp 
            $url = $this->generateUrl('reset_pass', ['token' => $token], 
            UrlGeneratorInterface ::ABSOLUTE_URL);
            //    on envoie le mail
            $message = (new \Swift_Message('Mot de passe oublié ? Réinitialisation !  '))
                ->setFrom('senesafari@symf.com')
                ->setTo($user->getEmail())
                //   on peut mettre un message direct comme on peut use un template
                //   text/html pour le formay du body
                // ->setBody("Bonjour,
                // Une demande de réinitialisation de votre mot de passe vient detre effectuée sur
                //  le site SENESafari.Veuillez cliquer sur le lien suivant :"
                //   . $url );
                ->setBody($this->renderView(
                    // templates/emails/registration.html.twig
                    'emails/resetPassword.html.twig',
                    ['url' => $url]
                ),
                'text/html'
            );
            $mailer->send($message);
            // on rajoute kle flash message
            $this->addFlash('message', 'Un mail de réinitialisation de votre mot de passe vient de vous être envoyé par mail.');
            return $this->redirectToRoute('app_login');
        }

        // on renvoie vers la page de login si le form nest pas soumis 
        return $this->render('security/reset_pass.html.twig', [
            'resetForm' => $form->createView()
        ]);
    }
    /**
     * @Route("/reset_password/{token}", name="reset_pass")
     */
     public function resetPassword(Request $request,  UserPasswordEncoderInterface $userEncoder, $token)
     { 
        // on cherche l'utilisateur avec le token fourni 
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['reset_token' => $token]);
        if(!$user)
        {
            $this->addFlash('danger','Token inconnu. Réesayez d\'obtenir un autre lien de réinitialisation de votre mot de passe.');
            return $this->redirectToRoute('app_login');
        }
        // si on a le form posté et envoyé
        if($request->isMethod('POST'))
        {
            // on supprime le token
            $user->setResetToken(null);
            // on chiffre le new pass
            
            // if($user->getPassword($user,$request->request->get("pass1")) == 
            //  $user->getPassword($user,$request->request->get("pass2"))){

            //  }
            $user->setPassword($userEncoder->encodePassword($user, $request->request->get('password')));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();   
            $this->addFlash('message', 'Mot de passe modifié avec succès.'); 
            return $this->redirectToRoute('app_login');

        }
        else
        {
            return $this->render('security/new_pass.html.twig',[
                'token' => $token,
             
            ]);
        }
    }
}
