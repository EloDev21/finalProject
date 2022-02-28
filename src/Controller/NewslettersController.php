<?php

namespace App\Controller;

use App\Entity\Newsletters\Newsletters;
use App\Entity\Newsletters\Users;
use App\Entity\Newsletters\Categories;
use App\Entity\User;
use App\Form\NewslettersType;   
use App\Form\NewslettersUsersType;
use App\Repository\Newsletters\NewslettersRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Email;

class NewslettersController extends AbstractController
{
    /**
     * @Route("/newsletters", name="newsletters")
     */
    public function index(Request $request , \Swift_Mailer $mailer): Response
    {
          // on instancie un objet de type Users (différent de User)
        //   Users = table contenant tous les utilisateurs inscrits à la newsletter != User =inscrits sur le site
        $user = new Users();
         // on cree un formulaire grace au FormBundle de symfony et on lui donne en parametre le 
        // form type (NewslettersUsersType) qui  décrit les champs de formulaire liés à un modèle (modele Newsletters/Users).
        $form = $this->createForm(NewslettersUsersType::class, $user);
          // si le formulaire est soumis et est valide$form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // creation d'un token avec encryptage sha256 et la proprieté uniqid
            $token =hash('sha256',uniqid());
            // on set à notre objet le token que l'on vient de créer
            $user->setValidationToken($token);
            // on fait appel à doctrine pour enregistrer en base de données
            $em=$this->getDoctrine()->getManager();
            // on prepare la mise en BDD
            $em->persist($user);
            // on sauvegarde en BDD
            $em->flush();
               //   on cree un email grace à la librairie SwiftMailer de Symfony
            $message = (new \Swift_Message('Mail de contact - SeneSAFARI'))
            // objet de l'email
            ->setSubject('Inscription à la newsletter')
            // expediteur
            ->setFrom('contact@senesafari.com')
              // destinataire en récuperant le mail de notre objet user créé plus haut 
            ->setTo($user->getEmail())
                //   Message contenu dans l'email 
        // redirection vers le fichier mail du dossier cart avec les parametres token et notre formulaire (form)
            ->setBody(
                $this->renderView(
                    // templates/emails/registration.html.twig
                    'emails/inscription.html.twig',
                    ['user' => $user,
                        'token' =>$token,
                         'form' => $form
                    ]
                ),
                'text/html'
            );
            // envoi du mail
            $mailer->send($message);
          
            // on garde le token . on le supprime pas pour le désabonnement
            $this->addFlash('attente','Votre inscription à la Newsletter est en attente de validation. Merci de consulter votre courriel.');
            // redirection vers la page accueil
            return $this->redirectToRoute('home');
        }
        return $this->render('newsletters/index.html.twig', [
            // crée la vue index du dossier newsletters et on lui envoie les données du formulaire que l'on vient de récuperer
            'form' => $form->createView()
        ]);
    }

    // on créée le lien de redirection ou confirmation
    /**
     * @Route("newsletters_confirm/{id}/{token}", name="newsletters_confirm")
     */
    public function confirm(Users $user , $token): Response
    {
        // si l'utisateur na pas de validationtoken ou s'il est différent de celui en base de données on le crée
        if($user->getValidationToken() != $token ){
            throw $this->createNotFoundException('Page non trouvée.');
        }
        // une fois qu'il clique sur le lien on set le la proprieté IsValid de notre objet user à true
        // en résumé on le set à true s'il clique sur le lien avec le token reçu par mail
            $user->setIsValid(true);
        // on fait appel à doctrine pour enregistrer en base de données
            $em=$this->getDoctrine()->getManager();
            // on prépare l'enregistrement de notre objet user
            $em->persist($user);
            // on sauvegarde l'objet
            $em->flush();
             // on rajoute un message flash pour informer que le compte est bien créé et on redirige vers la route avec le nom home
            $this->addFlash('activation_newsletter','Bravo votre compte vient d\'être acitvé.Nous sommes ravis de vous compter parmi nos membres ...');
            return $this->redirectToRoute('home'); 
        }
    /**
     * @Route("newsletters/prepare", name="newsletters_prepare")
     */
    public function prepare(Request $request): Response
    {
        // on instancie un objet newsletter de type Newsletters (entity)
        $newsletter = new Newsletters();
        //   creation d'un form grace au formtype importée (NewsletterType et on lui injecte l'objet que l'on vient de créer)
        $form = $this->createForm(NewslettersType::class, $newsletter);
         // lance la requete grace à httpfoundation Request
        $form->handleRequest($request);
           // si le formulaire est soumis et est valide
        if($form->isSubmitted() && $form->isValid())
        {
              // on fait appel à doctrine pour enregistrer en base de données
            $em = $this->getDoctrine()->getManager();
              // on prépare l'enregistrement de notre objet newsletter
            $em->persist($newsletter);
             // on sauvegarde l'objet
            $em->flush();
            // redirection sur la route nommée newsletters_list
            return $this->redirectToRoute('newsletters_list');
        }
           // on rajoute un message flash pour informer que la news est bien créé et on redirige vers la route avec le nom home
            $this->addFlash('creation_newsletter','Votre newsletter vient d\'être créée avec succès.');
              // redirection sur la page prepare du dossier newsletters et on lui envoie le formulaire créé
            return $this->render('newsletters/prepare.html.twig',[
                'form' => $form->createView()
            ]);
         }
    /**
    * @Route("newsletters/list", name="newsletters_list")
    */
    public function list (NewslettersRepository $newsletters):Response
         {
        //on utilise Doctrine pour acceder à notre BDD  
        // plus précisèment à la table newsletters à laquelle on applique la requete SQL findAll() 
        // pour afficher tous les elements présents dans notre table newsletters
        // on envoie le parametre (newsletters) à la vue list du dossier newsletters
                return $this->render('newsletters/list.html.twig',[
                    'newsletters' => $newsletters->findAll()
                ]);
         }
    /**
    * @Route("newsletters/send/{id}", name="newsletters_send")
    */
    public function send (Newsletters $newsletter,  \Swift_Mailer $mailer):Response
         {
            // on utilise Doctrine pour acceder à notre BDD et recuperer les users et leurs catégories de news
            $users = $newsletter->getCategories()->getUsers();
            //  dd($users);
             
            //  pour chaque utilisateur parmi nos utilisateurs récupérés plus haut 
             foreach ($users as $user) {
                //  si l'utilisateur a déjà confirmé son inscription
                 if($user->getIsValid())
                 {
                    //  nouvel email
                    $message = (new \Swift_Message('Mail de contact - SeneSAFARI'))
                    // objet du mail
                    ->setSubject('Inscription à la newsletter')
                    // expediteur
                    ->setFrom('senesafari@example.com')
                    // destinataire du mail 
                    ->setTo($user->getEmail())
                         //   Message contenu dans l'email 
                     // redirection vers le fichier newsletter du dossier emails avec les parametres user et newsletter
                    ->setBody(
                        $this->renderView(
                            // templates/emails/registration.html.twig
                            'emails/newsletter.html.twig',
                            ['user' => $user,
                            'newsletter' => $newsletter

                         
                              
                            ]
                        ),
                        'text/html'
                    );
                    // on envoie le mail 
                    $mailer->send($message);
                       

                    }
                }
                 // on set le la proprieté IsSent de notre objet newsletter à true
                // en résumé on le set à true s'il clique sur le lien avec le token reçu par mail
                $newsletter->setIsSent(true);
                   // on fait appel à l'entity manager pour enregistrer en base de données
                   // le bouton envoyer doit disparaitree puisque le isSent sera a 1 dans notre vue 
                 $em = $this->getDoctrine()->getManager();
                // on prépare l'enregistrement de notre objet newsletter
                 $em->persist($newsletter);
                // on sauvegarde l'objet
                 $em->flush();
                //  on retourne à la route nommée newsletters_list
                return $this->redirectToRoute('newsletters_list');
         }
          
    /**
    * @Route("newsletters/unsubscribe/{id}/{newsletter}/{token}", name="newsletters_unsubscribe")
    */
    public function unsubscribe (Newsletters $newsletter, Users $user,  $token):Response   
    {
        // si l'utisateur na pas de validationtoken ou s'il est différent de celui en base de données on le crée
        if($user->getValidationToken() != $token){
            throw $this->createNotFoundException('Page non trouvée.');
        }
          // on fait appel à l'entity manager pour enregistrer en base de données
        $em = $this->getDoctrine()->getManager();
        // si notre utilisateur est abonnée à plusieurs catégories
        if(count($user->getCategories()) > 1){
            // on le remove de la categorie selectionnee sil est abonné a plusieurs news.
            // s'il est seul on supprime la catégorie
            $user->removeCategory($newsletter->getCategories());
            // on stocke les changements effectués en bdd
            $em->persist($user);
        }
        // sil est abonné qu'à une seule categorie de newsletter on le supprime 
        else{
            $em->remove($user);
        }
        // sauvegarde des changements en BDD
        $em->flush();
        // on envoie un message flash comme quoi la newsletter est supprimée et on retourne à la liste des newsletters
        $this->addFlash('delete', 'Newsletter supprimée!');
        return $this->redirectToRoute('newsletters');
    } 

}

