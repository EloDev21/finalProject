<?php

namespace App\Controller;

use App\Form\ContactFormType;
use App\Entity\ContactForm;
use App\Notification\ContactNotification;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class ContactFormController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     */
    public function index(Request $request,  \Swift_Mailer $mailer): Response
    {
         // on instancie un objet de type cart
         $contactF = new ContactForm();
         //    on set la valeur de l'envoi du mail 
         $contactF->setCreatedAt(new \DateTimeImmutable('now'));
         
         // on cree un formulaire de contact grace à FormBunlde de Symfony de type ContactForm
        $formContact = $this->createForm(ContactFormType::class, $contactF);
         // on cree une requete grace à la librairie Request de HttpFoundation pour avoir acces au formulaire 
        $formContact->handleRequest($request);
          // si le formulaire est soumis et est valide
        if ($formContact->isSubmitted() && $formContact->isValid()) 
        {
            // snding the message with swiftmailer to the admin-team
             //   on cree un email grace à la librairie SwiftMailer de Symfony
            $message = (new \Swift_Message('Mail de contact - SeneSAFARI'))
            // expediteur en récuperant le mail de notre objet user créé plus haut 
            ->setFrom($contactF->getEmail())
            //destinataire
            ->setTo('senesafari@example.com')
              //   Message contenu dans l'email 
        // redirection vers le fichier mail du dossier cart avec les parametres user et commande
            ->setBody(
                $this->renderView(
                    // templates/emails/registration.html.twig
                    'emails/contact.html.twig',
                    ['contactF' => $contactF]
                ),
                'text/html'
            );
            //envoi du mail 
            $mailer->send($message);
            // saving the message in the entity*
            //on recupere toutes les donnees du formulaire
            $contactF = $formContact->getData();
            // on fait appel à doctrine pour enregistrer en base de données
            $em = $this->getDoctrine()->getManager();
            // on prepare la mise en BDD
            $em->persist($contactF);
            // on sauvegarde en BDD
            $em->flush();

           // on rajoute un message flash pour informer que le mail est bien envoyé et on redirige vers la route avec le nom home

            $this->addFlash('contact_email', 'Votre mail a été envoyé avec avec succès! Notre équipe vous reviendra dans les plus brefs délais. ');
            return $this->redirectToRoute('home');
        }
           // sinon on redirige vers le formulaire d'ajout et on récupere notre form pour pouvoir l'utilisier dans la vue twig 
        return $this->render('contact/index.html.twig', [
            'formContact' => $formContact->createView(),
        ]);
    }
}
